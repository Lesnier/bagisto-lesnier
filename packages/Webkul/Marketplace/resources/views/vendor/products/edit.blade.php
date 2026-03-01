@extends('marketplace::vendor.layouts.app')

@section('title', __('marketplace::app.vendor.products.edit'))
@section('page-title', __('marketplace::app.vendor.products.edit'))

@section('content')
    <div class="container mt-12 mb-12 flex justify-center">
        <div class="w-full max-w-[800px] border border-[#E9E9E9] rounded-lg p-8 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-3">
                    <a href="{{ route('vendor.products.index') }}" class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-100 hover:bg-gray-200 cursor-pointer text-gray-600 transition-all">
                        <i class="icon-sort-left text-xl"></i>
                    </a>
                    <h1 class="text-2xl font-bold text-gray-800">
                        {{ __('marketplace::app.vendor.products.edit') ?? 'Edit Product' }} (#{{ $product->id }})
                    </h1>
                </div>
            </div>

            @if(session('success'))
                <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('vendor.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Status Toggle (pure CSS, no Tailwind pseudo-elements needed) -->
                <style>
                    .toggle-wrap { display:flex; align-items:center; gap:.6rem; margin-bottom:1.25rem; }
                    .toggle-wrap .toggle-label { font-size:.875rem; font-weight:500; color:#374151; }
                    .toggle { position:relative; display:inline-block; width:44px; height:24px; }
                    .toggle input { opacity:0; width:0; height:0; }
                    .toggle-slider {
                        position:absolute; cursor:pointer; inset:0;
                        background:#d1d5db; border-radius:9999px;
                        transition:background .2s;
                    }
                    .toggle-slider::before {
                        content:''; position:absolute;
                        width:18px; height:18px; border-radius:50%;
                        background:#fff; top:3px; left:3px;
                        box-shadow:0 1px 3px rgba(0,0,0,.2);
                        transition:transform .2s;
                    }
                    .toggle input:checked + .toggle-slider { background:#2563eb; }
                    .toggle input:checked + .toggle-slider::before { transform:translateX(20px); }
                </style>
                <div class="toggle-wrap">
                    <input type="hidden" name="status" value="0">
                    <label class="toggle">
                        <input type="checkbox" name="status" value="1" {{ old('status', $product->status) ? 'checked' : '' }}>
                        <span class="toggle-slider"></span>
                    </label>
                    <span class="toggle-label">Activo</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div class="mb-5">
                        <label for="name" class="block text-sm font-medium text-gray-800 mb-2">
                            Nombre <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="name" name="name" value="{{ old('name', $product->name) }}" class="w-full px-4 py-3 rounded-lg border border-[#E9E9E9] outline-none focus:border-blue-600 transition-all @error('name') border-red-500 @enderror" required>
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Price -->
                    <div class="mb-5">
                        <label for="price" class="block text-sm font-medium text-gray-800 mb-2">
                            Precio <span class="text-red-500">*</span>
                        </label>
                        <input type="number" step="0.01" id="price" name="price" value="{{ old('price', $product->price) }}" class="w-full px-4 py-3 rounded-lg border border-[#E9E9E9] outline-none focus:border-blue-600 transition-all @error('price') border-red-500 @enderror" required>
                        @error('price')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <!-- Cost -->
                    <div class="mb-5">
                        <label for="cost" class="block text-sm font-medium text-gray-800 mb-2">Costo</label>
                        <input type="number" step="0.01" min="0" id="cost" name="cost"
                            value="{{ old('cost', $product->cost) }}"
                            placeholder="0.00"
                            class="w-full px-4 py-3 rounded-lg border border-[#E9E9E9] outline-none focus:border-blue-600 transition-all">
                    </div>

                    <!-- SKU -->
                    <div class="mb-5">
                        <label for="sku" class="block text-sm font-medium text-gray-800 mb-2">SKU</label>
                        <input type="text" id="sku" value="{{ $product->sku }}" class="w-full px-4 py-3 rounded-lg border border-[#E9E9E9] bg-gray-50 text-gray-500" disabled>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Weight -->
                    <div class="mb-5">
                        <label for="weight" class="block text-sm font-medium text-gray-800 mb-2">
                            Peso (kg) <span class="text-red-500">*</span>
                        </label>
                        <input type="number" step="0.01" id="weight" name="weight" value="{{ old('weight', $product->weight) }}" class="w-full px-4 py-3 rounded-lg border border-[#E9E9E9] outline-none focus:border-blue-600 transition-all @error('weight') border-red-500 @enderror" required>
                        @error('weight')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>

                <!-- Short Description -->
                <div class="mb-5">
                    <label for="short_description" class="block text-sm font-medium text-gray-800 mb-2">
                        Short Description
                    </label>
                    <textarea id="short_description" name="short_description" rows="3" class="w-full px-4 py-3 rounded-lg border border-[#E9E9E9] outline-none focus:border-blue-600 transition-all @error('short_description') border-red-500 @enderror">{{ old('short_description', $product->short_description) }}</textarea>
                    @error('short_description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description -->
                <div class="mb-8">
                    <label for="description" class="block text-sm font-medium text-gray-800 mb-2">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <textarea id="description" name="description" rows="6" class="w-full px-4 py-3 rounded-lg border border-[#E9E9E9] outline-none focus:border-blue-600 transition-all @error('description') border-red-500 @enderror" required>{{ old('description', $product->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ─── Shipping dimensions ───────────────────────────────── --}}
                <div class="mb-7">
                    <label class="block text-sm font-medium text-gray-800 mb-3">📦 Envío — Dimensiones</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label style="display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:.3rem;">Largo (cm)</label>
                            <input type="number" step="0.01" min="0" name="length"
                                value="{{ old('length', $product->length) }}"
                                placeholder="0"
                                class="w-full px-3 py-2 rounded-lg border border-[#E9E9E9] outline-none focus:border-blue-600 transition-all">
                        </div>
                        <div>
                            <label style="display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:.3rem;">Ancho (cm)</label>
                            <input type="number" step="0.01" min="0" name="width"
                                value="{{ old('width', $product->width) }}"
                                placeholder="0"
                                class="w-full px-3 py-2 rounded-lg border border-[#E9E9E9] outline-none focus:border-blue-600 transition-all">
                        </div>
                        <div>
                            <label style="display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:.3rem;">Alto (cm)</label>
                            <input type="number" step="0.01" min="0" name="height"
                                value="{{ old('height', $product->height) }}"
                                placeholder="0"
                                class="w-full px-3 py-2 rounded-lg border border-[#E9E9E9] outline-none focus:border-blue-600 transition-all">
                        </div>
                        <div>
                            <label style="display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:.3rem;">Peso (kg) *</label>
                            <input type="number" step="0.01" min="0" name="weight"
                                value="{{ old('weight', $product->weight) }}"
                                required
                                placeholder="0"
                                class="w-full px-3 py-2 rounded-lg border border-[#E9E9E9] outline-none focus:border-blue-600 transition-all">
                        </div>
                    </div>
                </div>

                {{-- ─── Settings ──────────────────────────────────────────────── --}}
                <div class="mb-7">
                    <label class="block text-sm font-medium text-gray-800 mb-3">⚙️ Configuración</label>
                    <div style="display:flex;flex-wrap:wrap;gap:1.25rem;">
                        {{-- Status already at top, but repeated here for completeness in the Settings block --}}
                        @foreach([
                            ['new',                 'Nuevo'],
                            ['featured',            'Destacado'],
                            ['visible_individually','Visible individualmente'],
                            ['guest_checkout',      'Permite checkout sin cuenta'],
                        ] as [$fieldName, $label])
                        <label style="display:flex;align-items:center;gap:.5rem;font-size:.875rem;color:#374151;cursor:pointer;">
                            <input type="hidden" name="{{ $fieldName }}" value="0">
                            <label class="toggle" style="margin:0;">
                                <input type="checkbox" name="{{ $fieldName }}" value="1"
                                    {{ old($fieldName, $product->{$fieldName} ?? 0) ? 'checked' : ''}}>
                                <span class="toggle-slider"></span>
                            </label>
                            {{ $label }}
                        </label>
                        @endforeach
                    </div>
                </div>

                {{-- ─── Image Upload ─────────────────────────────────────────── --}}
                <div class="mb-8">
                    <label class="block text-sm font-medium text-gray-800 mb-3">📷 Imágenes del Producto</label>

                    <style>
                        .img-dropzone {
                            border: 2px dashed #cbd5e1;
                            border-radius: .75rem;
                            padding: 2rem;
                            text-align: center;
                            cursor: pointer;
                            transition: border-color .2s, background .2s;
                            background: #f8fafc;
                        }
                        .img-dropzone:hover, .img-dropzone.drag-over {
                            border-color: #2563eb;
                            background: #eff6ff;
                        }
                        .img-dropzone .dz-icon { font-size: 2.5rem; margin-bottom: .5rem; }
                        .img-dropzone .dz-title { font-size: .95rem; font-weight: 600; color: #1e293b; }
                        .img-dropzone .dz-sub { font-size: .8rem; color: #94a3b8; margin-top: .25rem; }
                        .img-grid { display: flex; flex-wrap: wrap; gap: .75rem; margin-top: 1.25rem; }
                        .img-chip {
                            position: relative; width: 100px; height: 100px;
                            border-radius: .5rem; overflow: hidden;
                            border: 2px solid #e2e8f0;
                            box-shadow: 0 1px 4px rgba(0,0,0,.08);
                            transition: transform .15s;
                        }
                        .img-chip:hover { transform: scale(1.04); }
                        .img-chip img { width:100%; height:100%; object-fit:cover; display:block; }
                        .img-chip .img-remove {
                            position:absolute; top:4px; right:4px;
                            width:20px; height:20px; border-radius:50%;
                            background:rgba(0,0,0,.65); color:#fff;
                            display:flex; align-items:center; justify-content:center;
                            font-size:13px; font-weight:700; cursor:pointer;
                            line-height:1; border:none;
                            transition: background .15s;
                        }
                        .img-chip .img-remove:hover { background:#dc2626; }
                        .img-chip .img-badge {
                            position:absolute; bottom:0; left:0; right:0;
                            background:rgba(0,0,0,.45); color:#fff;
                            font-size:.62rem; text-align:center; padding:2px;
                        }
                        .img-error { color:#dc2626; font-size:.8rem; margin-top:.5rem; }
                    </style>

                    {{-- Hidden real file input --}}
                    <input type="file" id="imgInput" name="new_images[]" multiple accept="image/*" style="display:none">

                    {{-- Dropzone --}}
                    <div class="img-dropzone" id="imgDropzone">
                        <div class="dz-icon">🖼️</div>
                        <div class="dz-title">Arrastra imágenes aquí o haz clic para seleccionar</div>
                        <div class="dz-sub">PNG, JPG, WEBP · Máx. 2 MB por imagen · Múltiple selección permitida</div>
                    </div>
                    <div class="img-error" id="imgError"></div>

                    {{-- New image previews --}}
                    <div class="img-grid" id="newImgGrid"></div>

                    {{-- Existing images --}}
                    @if ($product->images && $product->images->count())
                        <p style="font-size:.8rem;font-weight:600;color:#64748b;margin-top:1.25rem;margin-bottom:.5rem;">
                            IMÁGENES ACTUALES
                        </p>
                        <div class="img-grid" id="existingImgGrid">
                            @foreach ($product->images as $img)
                                <div class="img-chip" id="img-chip-{{ $img->id }}">
                                    <img src="{{ Storage::url($img->path) }}" alt="product image">
                                    <button type="button" class="img-remove" title="Eliminar"
                                        onclick="markDeleteImage({{ $img->id }}, this.closest('.img-chip'))">&times;</button>
                                    <input type="hidden" name="existing_images[]" value="{{ $img->id }}" id="keep-{{ $img->id }}">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <script>
                const dropzone  = document.getElementById('imgDropzone');
                const fileInput = document.getElementById('imgInput');
                const newGrid   = document.getElementById('newImgGrid');
                const errBox    = document.getElementById('imgError');
                const MAX_MB    = 2;
                let   fileList  = [];

                dropzone.addEventListener('click', () => fileInput.click());

                dropzone.addEventListener('dragover',  e => { e.preventDefault(); dropzone.classList.add('drag-over'); });
                dropzone.addEventListener('dragleave', ()  => dropzone.classList.remove('drag-over'));
                dropzone.addEventListener('drop', e => {
                    e.preventDefault();
                    dropzone.classList.remove('drag-over');
                    handleFiles(e.dataTransfer.files);
                });

                fileInput.addEventListener('change', () => handleFiles(fileInput.files));

                function handleFiles(incoming) {
                    errBox.textContent = '';
                    Array.from(incoming).forEach(file => {
                        if (!file.type.startsWith('image/')) {
                            errBox.textContent = '⚠ Solo se permiten archivos de imagen.';
                            return;
                        }
                        if (file.size > MAX_MB * 1024 * 1024) {
                            errBox.textContent = `⚠ "${file.name}" supera ${MAX_MB} MB.`;
                            return;
                        }
                        fileList.push(file);
                        renderPreview(file, fileList.length - 1);
                    });
                    rebuildInput();
                }

                function renderPreview(file, idx) {
                    const reader = new FileReader();
                    reader.onload = e => {
                        const chip = document.createElement('div');
                        chip.className = 'img-chip';
                        chip.dataset.idx = idx;
                        chip.innerHTML = `
                            <img src="${e.target.result}" alt="preview">
                            <button type="button" class="img-remove" title="Quitar">&times;</button>
                            <span class="img-badge">${(file.size/1024).toFixed(0)} KB</span>
                        `;
                        chip.querySelector('.img-remove').addEventListener('click', () => {
                            fileList.splice(idx, 1);
                            chip.remove();
                            rebuildInput();
                        });
                        newGrid.appendChild(chip);
                    };
                    reader.readAsDataURL(file);
                }

                function rebuildInput() {
                    const dt = new DataTransfer();
                    fileList.forEach(f => dt.items.add(f));
                    fileInput.files = dt.files;
                }

                function markDeleteImage(id, chip) {
                    document.getElementById('keep-' + id).remove();
                    chip.style.opacity = '.35';
                    chip.style.pointerEvents = 'none';
                }
                </script>

                {{-- ─── Categories ────────────────────────────────────────── --}}
                <div class="mb-7">
                    <label class="block text-sm font-medium text-gray-800 mb-3">🗂 Categorías</label>
                    <div style="display:flex;flex-wrap:wrap;gap:.6rem;">
                        @foreach ($categories as $category)
                            @php $catName = $category->translations->first()?->name ?? $category->name ?? $category->id; @endphp
                            <label style="display:flex;align-items:center;gap:.35rem;font-size:.85rem;color:#374151;background:#f1f5f9;padding:.35rem .75rem;border-radius:.375rem;cursor:pointer;">
                                <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                    {{ in_array($category->id, $product->categories->pluck('id')->toArray()) ? 'checked' : '' }}
                                    style="accent-color:#6366f1;">
                                {{ $catName }}
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- ─── Inventory ──────────────────────────────────────────── --}}
                <div class="mb-7">
                    <label class="block text-sm font-medium text-gray-800 mb-3">📦 Inventario</label>
                    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:.75rem;">
                        @foreach ($inventorySources as $source)
                            @php
                                $inv = $product->inventories->firstWhere('inventory_source_id', $source->id);
                                $qty = $inv ? $inv->qty : 0;
                            @endphp
                            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:.5rem;padding:.85rem;">
                                <label style="display:block;font-size:.78rem;font-weight:600;color:#64748b;margin-bottom:.35rem;">
                                    {{ $source->name }}
                                </label>
                                <input type="number" name="inventories[{{ $source->id }}]" value="{{ old('inventories.'.$source->id, $qty) }}"
                                    min="0" step="1"
                                    style="width:100%;padding:.5rem;border:1px solid #d1d5db;border-radius:.375rem;font-size:.875rem;box-sizing:border-box;">
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- ─── Special Pricing ────────────────────────────────────── --}}
                <div class="mb-7">
                    <label class="block text-sm font-medium text-gray-800 mb-3">🏷 Precio Especial</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label style="display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:.3rem;">Precio especial</label>
                            <input type="number" step="0.01" min="0" name="special_price"
                                value="{{ old('special_price', $product->special_price) }}"
                                placeholder="0.00"
                                class="w-full px-4 py-3 rounded-lg border border-[#E9E9E9] outline-none focus:border-blue-600 transition-all">
                        </div>
                        <div>
                            <label style="display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:.3rem;">Válido desde</label>
                            <input type="date" name="special_price_from"
                                value="{{ old('special_price_from', $product->special_price_from) }}"
                                class="w-full px-4 py-3 rounded-lg border border-[#E9E9E9] outline-none focus:border-blue-600 transition-all">
                        </div>
                        <div>
                            <label style="display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:.3rem;">Válido hasta</label>
                            <input type="date" name="special_price_to"
                                value="{{ old('special_price_to', $product->special_price_to) }}"
                                class="w-full px-4 py-3 rounded-lg border border-[#E9E9E9] outline-none focus:border-blue-600 transition-all">
                        </div>
                    </div>
                </div>

                {{-- ─── SEO ────────────────────────────────────────────────── --}}
                <div class="mb-7">
                    <label class="block text-sm font-medium text-gray-800 mb-3">🔍 SEO</label>
                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label style="display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:.3rem;">
                                URL Key <span style="font-size:.75rem;color:#94a3b8;">(se auto-genera si se deja vacío)</span>
                            </label>
                            <input type="text" name="url_key"
                                value="{{ old('url_key', $product->url_key) }}"
                                placeholder="mi-producto-nombre"
                                class="w-full px-4 py-3 rounded-lg border border-[#E9E9E9] outline-none focus:border-blue-600 transition-all">
                        </div>
                        <div>
                            <label style="display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:.3rem;">Meta Título</label>
                            <input type="text" name="meta_title"
                                value="{{ old('meta_title', $product->meta_title) }}"
                                class="w-full px-4 py-3 rounded-lg border border-[#E9E9E9] outline-none focus:border-blue-600 transition-all">
                        </div>
                        <div>
                            <label style="display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:.3rem;">Meta Descripción</label>
                            <textarea name="meta_description" rows="2"
                                class="w-full px-4 py-3 rounded-lg border border-[#E9E9E9] outline-none focus:border-blue-600 transition-all">{{ old('meta_description', $product->meta_description) }}</textarea>
                        </div>
                        <div>
                            <label style="display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:.3rem;">Meta Keywords</label>
                            <input type="text" name="meta_keywords"
                                value="{{ old('meta_keywords', $product->meta_keywords) }}"
                                placeholder="palabra1, palabra2, …"
                                class="w-full px-4 py-3 rounded-lg border border-[#E9E9E9] outline-none focus:border-blue-600 transition-all">
                        </div>
                    </div>
                </div>


                {{-- ─── Customer Group Price ─────────────────────────────────── --}}
                <div class="mb-7" id="cgpSection">
                    <label class="block text-sm font-medium text-gray-800 mb-3">👥 Precios por Grupo de Clientes</label>
                    <style>
                        .cgp-table { width:100%; border-collapse:collapse; font-size:.85rem; }
                        .cgp-table th { background:#f1f5f9; padding:.5rem .75rem; text-align:left; font-weight:600; color:#64748b; font-size:.75rem; text-transform:uppercase; }
                        .cgp-table td { padding:.5rem .5rem; border-top:1px solid #f1f5f9; vertical-align:middle; }
                        .cgp-table input, .cgp-table select { width:100%; padding:.4rem .6rem; border:1px solid #d1d5db; border-radius:.375rem; font-size:.82rem; box-sizing:border-box; }
                        .cgp-add-btn { margin-top:.75rem; padding:.45rem 1rem; background:#ede9fe; color:#4f46e5; border:1px solid #c4b5fd; border-radius:.375rem; font-size:.82rem; cursor:pointer; }
                        .cgp-add-btn:hover { background:#c4b5fd; }
                        .cgp-remove { background:none; border:none; color:#dc2626; font-size:1.1rem; cursor:pointer; padding:0 .4rem; }
                    </style>
                    <table class="cgp-table" id="cgpTable">
                        <thead>
                            <tr>
                                <th>Grupo</th>
                                <th>Cant. mín.</th>
                                <th>Tipo</th>
                                <th>Precio / Descuento</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="cgpBody">
                            @forelse ($product->customer_group_prices ?? [] as $i => $cgp)
                            <tr>
                                <td>
                                    <select name="customer_group_prices[{{ $i }}][customer_group_id]">
                                        <option value="">Todos</option>
                                        @foreach (\Webkul\Customer\Models\CustomerGroup::all() as $g)
                                            <option value="{{ $g->id }}" {{ $cgp->customer_group_id == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="number" min="1" name="customer_group_prices[{{ $i }}][qty]" value="{{ $cgp->qty }}"></td>
                                <td>
                                    <select name="customer_group_prices[{{ $i }}][value_type]">
                                        <option value="fixed"      {{ $cgp->value_type == 'fixed'      ? 'selected' : '' }}>Fijo ($)</option>
                                        <option value="discount"   {{ $cgp->value_type == 'discount'   ? 'selected' : '' }}>Descuento (%)</option>
                                    </select>
                                </td>
                                <td><input type="number" step="0.01" min="0" name="customer_group_prices[{{ $i }}][value]" value="{{ $cgp->value }}"></td>
                                <td><button type="button" class="cgp-remove" onclick="this.closest('tr').remove()">🗑</button></td>
                            </tr>
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                    <button type="button" class="cgp-add-btn" onclick="addCgpRow()">+ Agregar precio de grupo</button>
                    <script>
                        let cgpIdx = {{ count($product->customer_group_prices ?? []) }};
                        function addCgpRow() {
                            const groups = @json(\Webkul\Customer\Models\CustomerGroup::all()->map(fn($g) => ['id'=>$g->id,'name'=>$g->name]));
                            let opts = '<option value="">Todos</option>' + groups.map(g => `<option value="${g.id}">${g.name}</option>`).join('');
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td><select name="customer_group_prices[${cgpIdx}][customer_group_id]">${opts}</select></td>
                                <td><input type="number" min="1" name="customer_group_prices[${cgpIdx}][qty]" value="1"></td>
                                <td>
                                    <select name="customer_group_prices[${cgpIdx}][value_type]">
                                        <option value="fixed">Fijo ($)</option>
                                        <option value="discount">Descuento (%)</option>
                                    </select>
                                </td>
                                <td><input type="number" step="0.01" min="0" name="customer_group_prices[${cgpIdx}][value]" value="0.00"></td>
                                <td><button type="button" class="cgp-remove" onclick="this.closest('tr').remove()">🗑</button></td>
                            `;
                            document.getElementById('cgpBody').appendChild(tr);
                            cgpIdx++;
                        }
                    </script>
                </div>

                <div style="text-align:right; padding-top:.5rem; border-top:1px solid #f1f5f9; margin-top:.5rem;">
                    <button type="submit" style="background:#2563eb; color:#fff; border:none; padding:.85rem 2.5rem; border-radius:.5rem; font-size:.95rem; font-weight:700; cursor:pointer; letter-spacing:.01em;" onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
                        💾 {{ __('marketplace::app.vendor.products.save-btn') ?? 'Guardar Producto' }}
                    </button>
                </div>

            </form>
        </div>
    </div>
@endsection
