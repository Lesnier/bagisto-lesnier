@extends('marketplace::vendor.layouts.app')

@section('title', __('marketplace::app.vendor.products.create'))
@section('page-title', __('marketplace::app.vendor.products.create'))

@section('content')
    <div class="container mt-12 mb-12 flex justify-center">
        <div class="w-full max-w-[600px] border border-[#E9E9E9] rounded-lg p-8 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div class="flex flex-col gap-2">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('vendor.products.index') }}" class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 cursor-pointer text-gray-600 transition-all">
                            <i class="icon-sort-left text-xl"></i>
                        </a>
                        <h1 class="text-2xl font-bold text-gray-800">
                            {{ __('marketplace::app.vendor.products.create') }}
                        </h1>
                    </div>
                </div>
            </div>

            <!-- Informational Alert -->
            <div class="mb-6 p-4 rounded-lg bg-blue-50 border border-blue-200 flex items-start gap-3">
                <i class="icon-information text-blue-600 text-xl mt-0.5"></i>
                <div class="text-blue-800 text-sm">
                    <strong>Paso 1 de 2:</strong> Para mantener la integridad del catálogo, el sistema primero requiere la configuración base (Tipo, Familia y SKU). <br>
                    Al hacer clic en "Guardar Producto", serás redirigido al configurador completo donde podrás ingresar el <strong>Nombre, Precio, Descripción e Imágenes</strong> del producto.
                </div>
            </div>

            <form action="{{ route('vendor.products.store') }}" method="POST">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Product Type -->
                    <div class="mb-5">
                        <label for="type" class="block text-sm font-medium text-gray-800 mb-2">
                            Type <span class="text-red-500">*</span>
                        </label>
                        <select id="type" name="type" class="w-full px-4 py-3 rounded-lg border border-[#E9E9E9] outline-none focus:border-blue-600 transition-all @error('type') border-red-500 @enderror" required>
                            <option value="simple">Simple</option>
                            <option value="virtual">Virtual</option>
                            <option value="downloadable">Downloadable</option>
                        </select>
                        @error('type')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Attribute Family -->
                    <div class="mb-5">
                        <label for="attribute_family_id" class="block text-sm font-medium text-gray-800 mb-2">
                            Attribute Family <span class="text-red-500">*</span>
                        </label>
                        <select id="attribute_family_id" name="attribute_family_id" class="w-full px-4 py-3 rounded-lg border border-[#E9E9E9] outline-none focus:border-blue-600 transition-all @error('attribute_family_id') border-red-500 @enderror" required>
                            <option value="1">Default</option>
                        </select>
                        @error('attribute_family_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- SKU -->
                <div class="mb-5">
                    <label for="sku" class="block text-sm font-medium text-gray-800 mb-2">
                        SKU <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="sku" name="sku" value="{{ old('sku') }}" placeholder="e.g. PROD-001" class="w-full px-4 py-3 rounded-lg border border-[#E9E9E9] outline-none focus:border-blue-600 transition-all @error('sku') border-red-500 @enderror" required>
                    @error('sku')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end mt-4">
                    <button type="submit" class="px-8 py-3 rounded-lg font-semibold cursor-pointer transition-all bg-blue-700 hover:bg-blue-800 text-white border-0">
                        Siguiente: Configurador de Producto →
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
