@extends('layouts.admin')

@section('title', 'Editar Producto')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <div>
                <h3 class="text-lg font-medium text-gray-900">Editar Reloj: {{ $product->name }}</h3>
                <p class="mt-1 text-sm text-gray-500">Actualiza la información del producto.</p>
            </div>
            @if($product->image_url)
                <img src="{{ $product->image_url }}" alt="Imagen" class="w-16 h-16 object-cover rounded border border-gray-200">
            @endif
        </div>
        
        <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre -->
                <div class="col-span-2">
                    <label for="name" class="block text-sm font-medium text-gray-700">Nombre del Producto <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $product->name) }}" required 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm border px-3 py-2 outline-none transition-colors @error('name') border-red-300 @enderror">
                    @error('name') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Marca -->
                <div>
                    <label for="brand" class="block text-sm font-medium text-gray-700">Marca</label>
                    <input type="text" name="brand" id="brand" value="{{ old('brand', $product->brand?->name) }}" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm border px-3 py-2 outline-none transition-colors">
                    @error('brand') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Precio -->
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700">Precio ($) <span class="text-red-500">*</span></label>
                    <input type="number" step="0.01" name="price" id="price" value="{{ old('price', $product->price) }}" required max="99999999.99"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm border px-3 py-2 outline-none transition-colors">
                    @error('price') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Stock -->
                <div>
                    <label for="stock" class="block text-sm font-medium text-gray-700">Stock <span class="text-red-500">*</span></label>
                    <input type="number" name="stock" id="stock" value="{{ old('stock', $product->stock) }}" required 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm border px-3 py-2 outline-none transition-colors">
                    @error('stock') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <!-- Imagen del Producto -->
                <div class="col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Imagen del Producto</label>
                    
                    <!-- Tabs -->
                    <div class="flex border-b border-gray-200 mb-4">
                        <button type="button" id="tab-upload" class="py-2 px-4 border-b-2 border-slate-900 text-sm font-medium text-slate-900 focus:outline-none transition-colors">
                            Subir Archivo
                        </button>
                        <button type="button" id="tab-url" class="py-2 px-4 border-b-2 border-transparent text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none transition-colors">
                            Enlace de Imagen (URL)
                        </button>
                    </div>

                    <!-- Upload File Pane -->
                    <div id="pane-upload" class="block">
                        <div id="dropzone" class="border-2 border-dashed border-gray-300 rounded-lg p-6 flex flex-col items-center justify-center cursor-pointer hover:border-slate-400 transition-colors bg-gray-50 relative group min-h-[160px]">
                            <input type="file" name="image" id="image" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            
                            <!-- Upload Prompt -->
                            <div class="text-center pointer-events-none group-hover:scale-105 transition-transform duration-200 {{ $product->image_url ? 'hidden' : '' }}" id="upload-prompt">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                    <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p class="mt-2 text-sm text-gray-600 font-semibold">Haz clic o arrastra una nueva imagen aquí</p>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF hasta 4MB</p>
                            </div>
                            
                            <!-- Image Preview Container -->
                            <div id="preview-container" class="w-full flex flex-col items-center {{ $product->image_url ? '' : 'hidden' }}">
                                <img id="image-preview" src="{{ $product->image_url ?? '#' }}" alt="Vista previa de la imagen" class="max-h-48 object-contain rounded-md shadow-sm border border-gray-200">
                                <button type="button" id="remove-image" class="mt-3 text-sm text-red-600 hover:text-red-800 font-medium flex items-center gap-1 z-20">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    Quitar imagen
                                </button>
                            </div>
                        </div>
                        @error('image') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <!-- URL Input Pane -->
                    <div id="pane-url" class="hidden">
                        <div class="relative rounded-md shadow-sm">
                            <input type="url" name="image_url" id="image_url" value="{{ old('image_url', $product->image_url) }}" placeholder="https://ejemplo.com/reloj.jpg"
                                class="block w-full rounded-md border-gray-300 focus:border-slate-500 focus:ring-slate-500 sm:text-sm border px-3 py-2 outline-none transition-colors @error('image_url') border-red-300 @enderror">
                        </div>
                        <p class="mt-2 text-xs text-gray-500">La imagen externa se descargará y guardará en tu Cloudinary automáticamente.</p>
                        @error('image_url') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <!-- Descripción -->
                <div class="col-span-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">Descripción</label>
                    <textarea name="description" id="description" rows="4" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-slate-500 focus:ring-slate-500 sm:text-sm border px-3 py-2 outline-none transition-colors">{{ old('description', $product->description) }}</textarea>
                    @error('description') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end space-x-3 border-t border-gray-100">
                <a href="{{ route('products.index') }}" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
                    Cancelar
                </a>
                <button type="submit" class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-slate-900 hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-900 transition-colors">
                    Actualizar Producto
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab switching logic
        const tabUpload = document.getElementById('tab-upload');
        const tabUrl = document.getElementById('tab-url');
        const paneUpload = document.getElementById('pane-upload');
        const paneUrl = document.getElementById('pane-url');
        const imageUrlInput = document.getElementById('image_url');
        const imageFileInput = document.getElementById('image');

        // Capture initial URL
        const initialUrl = "{{ $product->image_url ?? '' }}";

        tabUpload.addEventListener('click', () => {
            tabUpload.classList.add('border-slate-900', 'text-slate-900');
            tabUpload.classList.remove('border-transparent', 'text-gray-500');
            tabUrl.classList.remove('border-slate-900', 'text-slate-900');
            tabUrl.classList.add('border-transparent', 'text-gray-500');

            paneUpload.classList.remove('hidden');
            paneUpload.classList.add('block');
            paneUrl.classList.remove('block');
            paneUrl.classList.add('hidden');
            
            // Restore file preview if file was chosen or initial image exists
            if (imageFileInput.files.length > 0 || initialUrl) {
                document.getElementById('upload-prompt').classList.add('hidden');
                document.getElementById('preview-container').classList.remove('hidden');
                if (initialUrl && imageFileInput.files.length === 0) {
                    document.getElementById('image-preview').src = initialUrl;
                }
            } else {
                document.getElementById('upload-prompt').classList.remove('hidden');
                document.getElementById('preview-container').classList.add('hidden');
            }
        });

        tabUrl.addEventListener('click', () => {
            tabUrl.classList.add('border-slate-900', 'text-slate-900');
            tabUrl.classList.remove('border-transparent', 'text-gray-500');
            tabUpload.classList.remove('border-slate-900', 'text-slate-900');
            tabUpload.classList.add('border-transparent', 'text-gray-500');

            paneUrl.classList.remove('hidden');
            paneUrl.classList.add('block');
            paneUpload.classList.remove('block');
            paneUpload.classList.add('hidden');
        });

        // File input preview logic
        imageFileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    document.getElementById('image-preview').src = event.target.result;
                    document.getElementById('upload-prompt').classList.add('hidden');
                    document.getElementById('preview-container').classList.remove('hidden');
                    // Reset url text input when file is selected
                    imageUrlInput.value = '';
                };
                reader.readAsDataURL(file);
            }
        });

        // Remove image logic
        document.getElementById('remove-image').addEventListener('click', function(e) {
            e.stopPropagation(); // prevent triggering click on dropzone
            imageFileInput.value = '';
            imageUrlInput.value = '';
            document.getElementById('upload-prompt').classList.remove('hidden');
            document.getElementById('preview-container').classList.add('hidden');
        });
        
        // Drag & Drop visual highlights
        const dropzone = document.getElementById('dropzone');
        ['dragenter', 'dragover'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropzone.classList.add('border-slate-500', 'bg-slate-100');
            }, false);
        });
        ['dragleave', 'drop'].forEach(eventName => {
            dropzone.addEventListener(eventName, (e) => {
                e.preventDefault();
                dropzone.classList.remove('border-slate-500', 'bg-slate-100');
            }, false);
        });
    });
</script>
@endsection
