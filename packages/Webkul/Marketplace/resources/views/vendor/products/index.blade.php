@extends('marketplace::vendor.layouts.app')

@section('title', __('marketplace::app.vendor.products.title'))
@section('page-title', __('marketplace::app.vendor.products.title'))

@section('content')

<style>
    /* ── Toolbar ──────────────────────────────────────────────────────── */
    .p-toolbar { display:flex; flex-wrap:wrap; gap:.6rem; align-items:center; margin-bottom:1rem; }
    .p-toolbar .search-input {
        flex:1; min-width:180px; padding:.5rem .85rem;
        border:1px solid #e2e8f0; border-radius:.375rem; font-size:.875rem; color:#1e293b;
        outline:none;
    }
    .p-toolbar .search-input:focus { border-color:#6366f1; }
    .p-toolbar select {
        padding:.5rem .75rem; border:1px solid #e2e8f0; border-radius:.375rem;
        font-size:.8rem; color:#374151; background:#fff; cursor:pointer; outline:none;
    }
    .p-toolbar select:focus { border-color:#6366f1; }

    /* ── Buttons ──────────────────────────────────────────────────────── */
    .btn-primary-solid {
        padding:.5rem 1.1rem; background:#6366f1; color:#fff;
        border-radius:.375rem; text-decoration:none; font-size:.875rem;
        border:none; cursor:pointer; white-space:nowrap;
        display:inline-flex; align-items:center; gap:.3rem;
    }
    .btn-primary-solid:hover { background:#4f46e5; color:#fff; }
    .btn-outline {
        padding:.5rem 1rem; background:#fff; color:#374151;
        border:1px solid #e2e8f0; border-radius:.375rem; font-size:.8rem;
        cursor:pointer; white-space:nowrap; text-decoration:none;
        display:inline-flex; align-items:center; gap:.3rem;
    }
    .btn-outline:hover { border-color:#6366f1; color:#6366f1; }
    .btn-outline.green { color:#059669; border-color:#d1fae5; }
    .btn-outline.green:hover { background:#d1fae5; color:#059669; border-color:#059669; }

    /* ── Table ─────────────────────────────────────────────────────────── */
    .p-table { width:100%; border-collapse:collapse; background:#fff; border:1px solid #e5e7eb; border-radius:.5rem; overflow:hidden; }
    .p-table th { background:#f1f5f9; text-align:left; padding:.6rem .9rem; font-size:.75rem; font-weight:600; color:#64748b; text-transform:uppercase; white-space:nowrap; }
    .p-table td { padding:.65rem .9rem; font-size:.85rem; color:#374151; border-top:1px solid #f1f5f9; vertical-align:middle; }
    .p-table tr:hover td { background:#fafafa; }
    .btn-sm { padding:.28rem .65rem; font-size:.78rem; border-radius:.375rem; text-decoration:none; display:inline-block; }
    .btn-edit { background:#e0e7ff; color:#3730a3; }
    .btn-edit:hover { background:#c7d2fe; }
    .btn-del  { background:#fee2e2; color:#991b1b; border:none; cursor:pointer; font-family:inherit; }
    .btn-del:hover { background:#fecaca; }

    /* ── Thumbnail ─────────────────────────────────────────────────────── */
    .product-thumb { width:44px; height:44px; border-radius:.375rem; object-fit:cover; border:1px solid #e2e8f0; }
    .no-thumb { width:44px; height:44px; border-radius:.375rem; background:#f1f5f9; display:flex; align-items:center; justify-content:center; font-size:1.2rem; border:1px solid #e2e8f0; }

    /* ── Badge ─────────────────────────────────────────────────────────── */
    .badge { display:inline-block; padding:.15rem .5rem; border-radius:9999px; font-size:.7rem; font-weight:600; text-transform:uppercase; }
    .badge-active { background:#d1fae5; color:#065f46; }
    .badge-inactive { background:#fee2e2; color:#991b1b; }

    /* ── Import modal ───────────────────────────────────────────────────── */
    .modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.45); z-index:999; align-items:center; justify-content:center; }
    .modal-overlay.open { display:flex; }
    .modal-box { background:#fff; border-radius:.75rem; padding:1.75rem; width:100%; max-width:480px; box-shadow:0 20px 60px rgba(0,0,0,.2); }
    .modal-box h3 { font-size:1rem; font-weight:700; color:#1e293b; margin-bottom:1rem; }
    .import-drop {
        border:2px dashed #cbd5e1; border-radius:.5rem; padding:1.5rem;
        text-align:center; cursor:pointer; background:#f8fafc;
        transition:border-color .2s, background .2s;
    }
    .import-drop.drag-over { border-color:#6366f1; background:#f0f4ff; }
    .import-drop p { font-size:.85rem; color:#64748b; margin:.25rem 0 0; }
    .import-file-name { font-size:.8rem; color:#059669; margin-top:.5rem; }
    .modal-actions { display:flex; gap:.75rem; justify-content:flex-end; margin-top:1.25rem; }
</style>

{{-- ── Toolbar ──────────────────────────────────────────────────────────── --}}
<form method="GET" action="{{ route('vendor.products.index') }}" class="p-toolbar" id="filterForm">
    <input type="text" name="search" value="{{ request('search') }}"
           class="search-input" placeholder="🔍  Buscar por nombre o SKU…">

    <select name="type" onchange="document.getElementById('filterForm').submit()">
        <option value="">Todos los tipos</option>
        <option value="simple"       {{ request('type') === 'simple'       ? 'selected' : '' }}>Simple</option>
        <option value="virtual"      {{ request('type') === 'virtual'      ? 'selected' : '' }}>Virtual</option>
        <option value="downloadable" {{ request('type') === 'downloadable' ? 'selected' : '' }}>Descargable</option>
    </select>

    <select name="status" onchange="document.getElementById('filterForm').submit()">
        <option value="">Todos los estados</option>
        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Activo</option>
        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Inactivo</option>
    </select>

    <button type="submit" class="btn-outline">Filtrar</button>

    {{-- Spacer --}}
    <span style="flex:1"></span>

    {{-- Download template --}}
    <a href="{{ route('vendor.products.template') }}" class="btn-outline green" title="Descargar plantilla CSV">
        ⬇ Plantilla CSV
    </a>

    {{-- Import button --}}
    <button type="button" class="btn-outline" onclick="document.getElementById('importModal').classList.add('open')"
            title="Importar productos desde CSV">
        📂 Importar CSV
    </button>

    {{-- Add product --}}
    <a href="{{ route('vendor.products.create') }}" class="btn-primary-solid">
        + {{ __('marketplace::app.vendor.products.add') }}
    </a>
</form>

{{-- ── Import modal ──────────────────────────────────────────────────────── --}}
<div class="modal-overlay" id="importModal">
    <div class="modal-box">
        <h3>📂 Importar productos desde CSV</h3>

        <form method="POST" action="{{ route('vendor.products.import') }}" enctype="multipart/form-data" id="importForm">
            @csrf
            <input type="file" name="csv_file" id="csvFile" accept=".csv,.txt" style="display:none">

            <div class="import-drop" id="importDrop">
                <div style="font-size:2rem">📄</div>
                <strong style="font-size:.9rem;color:#1e293b;">Arrastra tu CSV aquí o haz clic para seleccionar</strong>
                <p>Solo archivos .csv · Máx. 5 MB</p>
            </div>
            <div class="import-file-name" id="importFileName"></div>

            <p style="font-size:.78rem;color:#94a3b8;margin-top:.75rem;">
                ¿No tienes la plantilla?
                <a href="{{ route('vendor.products.template') }}" style="color:#6366f1;">Descárgala aquí</a>
            </p>

            <div class="modal-actions">
                <button type="button" class="btn-outline"
                        onclick="document.getElementById('importModal').classList.remove('open')">
                    Cancelar
                </button>
                <button type="submit" class="btn-primary-solid" id="importSubmitBtn" disabled
                        style="opacity:.5;cursor:not-allowed;">
                    Importar
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── Products table ────────────────────────────────────────────────────── --}}
@if ($products->isEmpty())
    <p style="color:#94a3b8;font-size:.875rem;text-align:center;padding:2rem;">
        {{ __('marketplace::app.vendor.products.empty') }}
    </p>
@else
    <table class="p-table">
        <thead>
            <tr>
                <th>Foto</th>
                <th>#</th>
                <th>{{ __('marketplace::app.vendor.products.name') }}</th>
                <th>SKU</th>
                <th>Tipo</th>
                <th>Estado</th>
                <th>{{ __('marketplace::app.vendor.products.actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($products as $product)
            <tr>
                {{-- Thumbnail --}}
                <td>
                    @if ($product->images && $product->images->first())
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($product->images->first()->path) }}"
                             alt="thumb" class="product-thumb">
                    @else
                        <div class="no-thumb">📦</div>
                    @endif
                </td>
                <td style="color:#94a3b8;font-size:.8rem;">{{ $product->id }}</td>
                <td>{{ $product->name ?? '—' }}</td>
                <td style="font-family:monospace;font-size:.8rem;">{{ $product->sku }}</td>
                <td>{{ ucfirst($product->type) }}</td>
                <td>
                    <span class="badge {{ $product->status ? 'badge-active' : 'badge-inactive' }}">
                        {{ $product->status ? 'Activo' : 'Inactivo' }}
                    </span>
                </td>
                <td>
                    <div style="display:flex;gap:.4rem;align-items:center;">
                        <a href="{{ route('vendor.products.edit', $product->id) }}" class="btn-sm btn-edit">
                            ✏ Editar
                        </a>
                        <form method="POST" action="{{ route('vendor.products.destroy', $product->id) }}"
                              onsubmit="return confirm('{{ __('marketplace::app.vendor.products.confirm-delete') }}')"
                              style="display:flex;align-items:center;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn-sm btn-del"
                                    style="background:#fee2e2;color:#991b1b;">
                                🗑 Eliminar
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:1rem;">{{ $products->withQueryString()->links() }}</div>
@endif

<script>
/* ── Import modal drag-and-drop ────────────────────────────────────── */
const importDrop  = document.getElementById('importDrop');
const csvFile     = document.getElementById('csvFile');
const fileNameEl  = document.getElementById('importFileName');
const submitBtn   = document.getElementById('importSubmitBtn');

importDrop.addEventListener('click', () => csvFile.click());

importDrop.addEventListener('dragover', e => {
    e.preventDefault();
    importDrop.classList.add('drag-over');
});
importDrop.addEventListener('dragleave', () => importDrop.classList.remove('drag-over'));
importDrop.addEventListener('drop', e => {
    e.preventDefault();
    importDrop.classList.remove('drag-over');
    if (e.dataTransfer.files.length) {
        setFile(e.dataTransfer.files[0]);
    }
});

csvFile.addEventListener('change', () => {
    if (csvFile.files.length) setFile(csvFile.files[0]);
});

function setFile(file) {
    const dt = new DataTransfer();
    dt.items.add(file);
    csvFile.files = dt.files;
    fileNameEl.textContent = '✅ ' + file.name;
    submitBtn.disabled = false;
    submitBtn.style.opacity = '1';
    submitBtn.style.cursor  = 'pointer';
}

/* Close modal when clicking the overlay backdrop */
document.getElementById('importModal').addEventListener('click', function(e) {
    if (e.target === this) this.classList.remove('open');
});
</script>

@endsection
