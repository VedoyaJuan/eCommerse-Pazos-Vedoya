<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Exceptions\MPApiException;

class MercadoPagoController extends Controller
{
    public function webhook(Request $request)
    {
        $type  = $request->query('type') ?? $request->input('type');
        $topic = $request->query('topic') ?? $request->input('topic');

        // MP envía dos formatos: IPN (topic=payment) y Webhooks (type=payment)
        if ($type !== 'payment' && $topic !== 'payment') {
            return response()->json(['status' => 'ignored'], 200);
        }

        $paymentId = $request->input('data.id') ?? $request->query('id');

        if (!$paymentId) {
            return response()->json(['error' => 'No payment id'], 400);
        }

        try {
            MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));

            $client  = new PaymentClient();
            $payment = $client->get((int) $paymentId);

            $externalReference = $payment->external_reference;
            $mpStatus          = $payment->status;

            if (!$externalReference) {
                return response()->json(['status' => 'no_reference'], 200);
            }

            $order = Order::find((int) $externalReference);

            if (!$order) {
                Log::warning('MP webhook: order not found', ['external_reference' => $externalReference]);
                return response()->json(['status' => 'order_not_found'], 200);
            }

            // No pisar estados finales ya establecidos
            if (in_array($order->status, ['delivered', 'cancelled', 'anulado'])) {
                return response()->json(['status' => 'skipped_final_state'], 200);
            }

            $newStatus = match ($mpStatus) {
                'approved'  => 'processing',
                'rejected'  => 'cancelled',
                'cancelled' => 'cancelled',
                default     => null,
            };

            if ($newStatus === null) {
                $order->update(['mp_payment_id' => (string) $paymentId]);
                return response()->json(['status' => 'payment_status_unchanged'], 200);
            }

            DB::transaction(function () use ($order, $newStatus, $paymentId) {
                if ($newStatus === 'cancelled') {
                    foreach ($order->items as $item) {
                        if ($item->product_id) {
                            $product = Product::lockForUpdate()->find($item->product_id);
                            if ($product) {
                                $product->increment('stock', $item->quantity);
                            }
                        }
                    }
                }

                $order->update([
                    'status'        => $newStatus,
                    'mp_payment_id' => (string) $paymentId,
                ]);
            });

            return response()->json(['status' => 'updated', 'new_status' => $newStatus], 200);

        } catch (MPApiException $e) {
            Log::error('MP webhook MPApiException', [
                'payment_id' => $paymentId,
                'content'    => $e->getApiResponse()->getContent(),
            ]);
            return response()->json(['status' => 'error_logged'], 200);
        } catch (\Exception $e) {
            Log::error('MP webhook unexpected error', [
                'payment_id' => $paymentId,
                'message'    => $e->getMessage(),
            ]);
            return response()->json(['status' => 'error_logged'], 200);
        }
    }
}
