<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('brand');
        $like = \Illuminate\Support\Facades\Schema::getConnection()->getDriverName() === 'sqlite' ? 'like' : 'ilike';

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search, $like) {
                $q->where('name', $like, "%{$search}%")
                  ->orWhereHas('brand', function ($bq) use ($search, $like) {
                      $bq->where('name', $like, "%{$search}%");
                  })
                  ->orWhere('description', $like, "%{$search}%");
            });
        }

        if ($request->filled('brand')) {
            $query->whereHas('brand', function ($bq) use ($request, $like) {
                $bq->where('name', $like, "%{$request->brand}%");
            });
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->boolean('in_stock')) {
            $query->where('stock', '>', 0);
        }

        $products = $query->orderBy('name')->paginate(20);

        return ProductResource::collection($products);
    }

    public function show(Product $product)
    {
        return new ProductResource($product);
    }

    public function store(Request $request, \App\Services\CloudinaryService $cloudinary)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0|max:99999999.99',
            'stock'       => 'required|integer|min:0',
            'brand'       => 'nullable|string|max:255',
            'image_url'   => 'nullable|url',
            'image'       => 'nullable|image|max:4096',
        ]);

        $brandId = null;
        if (!empty($validated['brand'])) {
            $brand = \App\Models\Brand::firstOrCreate(['name' => $validated['brand']]);
            $brandId = $brand->id;
        }

        $data = $validated;
        unset($data['brand'], $data['image']);
        $data['brand_id'] = $brandId;

        if ($request->hasFile('image')) {
            $uploadedUrl = $cloudinary->upload($request->file('image'));
            if ($uploadedUrl) {
                $data['image_url'] = $uploadedUrl;
            }
        } elseif ($request->filled('image_url')) {
            $uploadedUrl = $cloudinary->upload($request->input('image_url'));
            if ($uploadedUrl) {
                $data['image_url'] = $uploadedUrl;
            }
        }

        $product = Product::create($data);

        return new ProductResource($product);
    }

    public function update(Request $request, Product $product, \App\Services\CloudinaryService $cloudinary)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'price'       => 'required|numeric|min:0|max:99999999.99',
            'stock'       => 'required|integer|min:0',
            'brand'       => 'nullable|string|max:255',
            'image_url'   => 'nullable|url',
            'image'       => 'nullable|image|max:4096',
        ]);

        $brandId = null;
        if (!empty($validated['brand'])) {
            $brand = \App\Models\Brand::firstOrCreate(['name' => $validated['brand']]);
            $brandId = $brand->id;
        }

        $data = $validated;
        unset($data['brand'], $data['image']);
        $data['brand_id'] = $brandId;

        if ($request->hasFile('image')) {
            $uploadedUrl = $cloudinary->upload($request->file('image'));
            if ($uploadedUrl) {
                $data['image_url'] = $uploadedUrl;
            }
        } elseif ($request->filled('image_url') && $request->input('image_url') !== $product->image_url) {
            $uploadedUrl = $cloudinary->upload($request->input('image_url'));
            if ($uploadedUrl) {
                $data['image_url'] = $uploadedUrl;
            }
        }

        $product->update($data);

        return new ProductResource($product);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'message' => 'Producto eliminado exitosamente.'
        ]);
    }
}
