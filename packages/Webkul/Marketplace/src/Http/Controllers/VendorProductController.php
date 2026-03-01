<?php

namespace Webkul\Marketplace\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Webkul\Marketplace\Repositories\VendorRepository;
use Webkul\Product\Models\ProductImage;
use Webkul\Product\Repositories\ProductRepository;

class VendorProductController extends Controller
{
    public function __construct(
        protected VendorRepository $vendorRepository,
        protected ProductRepository $productRepository,
    ) {}

    private function currentVendor()
    {
        return $this->vendorRepository->findApprovedByCustomer(
            Auth::guard('customer')->id()
        );
    }

    public function index(Request $request)
    {
        $vendor = $this->currentVendor();
        $search = $request->input('search');
        $type   = $request->input('type');
        $status = $request->input('status');

        $products = $this->productRepository->scopeQuery(function ($query) use ($vendor, $search, $type, $status) {
            $query->where('products.vendor_id', $vendor->id)->latest('products.created_at');
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('products.sku', 'like', "%{$search}%")
                      ->orWhereHas('translations', fn($t) => $t->where('name', 'like', "%{$search}%"));
                });
            }
            if ($type) {
                $query->where('products.type', $type);
            }
            if (!is_null($status) && $status !== '') {
                // status lives in product_flat, not in products
                $locale  = core()->getCurrentLocale()->code;
                $channel = core()->getCurrentChannelCode();
                $query->whereHas('flat', function ($q) use ($status, $locale, $channel) {
                    $q->where('status', (int) $status)
                      ->where('locale', $locale)
                      ->where('channel', $channel);
                });
            }
            return $query;
        })->with('images')->paginate(limit: 15);

        return view('marketplace::vendor.products.index', compact('vendor', 'products'));
    }

    public function create()
    {
        $vendor = $this->currentVendor();

        return view('marketplace::vendor.products.create', compact('vendor'));
    }

    public function store(Request $request)
    {
        $vendor = $this->currentVendor();

        $request->validate([
            'type'                => 'required|in:simple,virtual,downloadable',
            'attribute_family_id' => 'required|integer',
            'sku'                 => 'required|string|unique:products,sku',
        ]);

        $data = array_merge($request->all(), [
            'vendor_id' => $vendor->id,
            // Basic required attributes for Bagisto product creation
        ]);

        /** @var \Webkul\Product\Models\Product $product */
        $product = $this->productRepository->create($data);

        // Assign to the specific vendor explicitly since vendor_id is not fillable in core
        $product->vendor_id = $vendor->id;
        $product->save();

        session()->flash('success', __('marketplace::app.vendor.products.created'));

        return redirect()->route('vendor.products.edit', $product->id);
    }

    public function edit(\Webkul\Product\Models\Product $product)
    {
        $vendor = $this->currentVendor();

        Gate::authorize('marketplace.vendor.manage-product', $product);

        $categories       = \Webkul\Category\Models\Category::with('translations')->whereNotNull('parent_id')->orderBy('position')->get();
        $inventorySources = \Webkul\Inventory\Models\InventorySource::where('status', 1)->get();
        $currentChannel   = core()->getCurrentChannel();

        return view('marketplace::vendor.products.edit', compact(
            'vendor', 'product', 'categories', 'inventorySources', 'currentChannel'
        ));
    }

    public function update(Request $request, \Webkul\Product\Models\Product $product)
    {
        Gate::authorize('marketplace.vendor.manage-product', $product);

        $request->validate([
            'name'               => 'required|string',
            'price'              => 'required|numeric|min:0',
            'weight'             => 'required|numeric|min:0',
            'description'        => 'required|string',
            'status'             => 'boolean',
            'categories'         => 'nullable|array',
            'inventories'        => 'nullable|array',
            'new_images.*'       => 'nullable|image|max:2048',
            'url_key'            => 'nullable|string',
            'special_price'      => 'nullable|numeric|min:0',
            'special_price_from' => 'nullable|date',
            'special_price_to'   => 'nullable|date|after_or_equal:special_price_from',
            'cost'               => 'nullable|numeric|min:0',
            'length'             => 'nullable|numeric|min:0',
            'width'              => 'nullable|numeric|min:0',
            'height'             => 'nullable|numeric|min:0',
            'customer_group_prices'          => 'nullable|array',
            'customer_group_prices.*.value'  => 'required_with:customer_group_prices|numeric|min:0',
        ]);

        $locale  = core()->getCurrentLocale();
        $channel = core()->getCurrentChannel();

        // Build $data exactly as Bagisto admin does so productRepository->update()
        // correctly populates product_attribute_values and product_flat
        $data = [
            // Scope keys — saveValues() uses these to identify which DB row to write
            'channel'             => $channel->code,
            'locale'              => $locale->code,

            // Flat attribute codes — saveValues() iterates attribute_family->custom_attributes
            // and looks for $data[$attribute->code]
            'name'                => $request->name,
            'url_key'             => $request->url_key ?: \Illuminate\Support\Str::slug($request->name),
            'price'               => $request->price,
            'special_price'       => $request->special_price ?: null,
            'special_price_from'  => $request->special_price_from ?: null,
            'special_price_to'    => $request->special_price_to ?: null,
            'weight'              => $request->weight,
            'description'         => $request->description,
            'short_description'   => $request->short_description ?? '',
            'meta_title'          => $request->meta_title ?? '',
            'meta_description'    => $request->meta_description ?? '',
            'meta_keywords'       => $request->meta_keywords ?? '',
            'status'              => $request->boolean('status') ? 1 : 0,
            'visible_individually'=> $request->boolean('visible_individually') ? 1 : 0,
            'new'                 => $request->boolean('new') ? 1 : 0,
            'featured'            => $request->boolean('featured') ? 1 : 0,
            'guest_checkout'      => $request->boolean('guest_checkout') ? 1 : 0,
            'cost'                => $request->cost ?: null,
            'length'              => $request->length ?: null,
            'width'               => $request->width ?: null,
            'height'              => $request->height ?: null,

            // Relation syncs
            'channels'            => [$channel->id],
            'categories'          => $request->input('categories', []),
            'inventories'         => $request->input('inventories', []),
            // Customer group prices — handled by productCustomerGroupPriceRepository inside AbstractType::update()
            'customer_group_prices' => $request->input('customer_group_prices', []),
        ];

        // Build images['files'] the way productMediaRepository->upload() expects:
        // existing IDs (non-UploadedFile) → kept; new UploadedFile objects → stored
        $imgFiles = [];
        foreach ($request->input('existing_images', []) as $existingId) {
            $imgFiles[(int) $existingId] = (int) $existingId;
        }
        if ($request->hasFile('new_images')) {
            foreach ($request->file('new_images') as $idx => $file) {
                $imgFiles['image_new_'.$idx] = $file;
            }
        }
        $data['images'] = ['files' => $imgFiles];

        \Illuminate\Support\Facades\Event::dispatch('catalog.product.update.before', $product->id);

        $this->productRepository->update($data, $product->id);

        \Illuminate\Support\Facades\Event::dispatch('catalog.product.update.after', $product);

        session()->flash('success', __('marketplace::app.vendor.products.updated'));

        return redirect()->route('vendor.products.edit', $product->id);
    }


    public function destroy(\Webkul\Product\Models\Product $product)
    {
        Gate::authorize('marketplace.vendor.manage-product', $product);

        $this->productRepository->delete($product->id);

        session()->flash('success', __('marketplace::app.vendor.products.deleted'));

        return redirect()->route('vendor.products.index');
    }

    /** Download a blank CSV template. */
    public function downloadTemplate()
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="productos_plantilla.csv"',
            'Pragma'              => 'no-cache',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // BOM for Excel
            fputcsv($handle, ['type', 'sku', 'name', 'price', 'weight', 'status', 'short_description', 'description']);
            fputcsv($handle, ['simple', 'PROD-001', 'Nombre del producto', '29.99', '0.5', '1', 'Descripción corta', 'Descripción larga']);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /** Bulk-import products from an uploaded CSV. */
    public function import(Request $request)
    {
        $request->validate(['csv_file' => 'required|file|mimes:csv,txt|max:5120']);

        $vendor  = $this->currentVendor();
        $locale  = core()->getCurrentLocale();
        $channel = core()->getCurrentChannelCode();
        $handle  = fopen($request->file('csv_file')->getRealPath(), 'r');

        // Skip UTF-8 BOM if present
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            fseek($handle, 0);
        }

        $headers = fgetcsv($handle);
        $created = 0;
        $errors  = [];

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 4) { continue; }
            $data = array_combine($headers, $row);

            if (empty($data['sku']) || empty($data['type'])) {
                $errors[] = "Fila omitida: SKU o tipo vacío.";
                continue;
            }

            if (\Webkul\Product\Models\Product::where('sku', $data['sku'])->exists()) {
                $errors[] = "SKU '{$data['sku']}' ya existe, omitido.";
                continue;
            }

            $productData = [
                'type'                => $data['type']              ?? 'simple',
                'sku'                 => $data['sku'],
                'attribute_family_id' => 1,
                'vendor_id'           => $vendor->id,
                'channel'             => $channel,
                'locale'              => $locale->code,
                'name'                => $data['name']              ?? '',
                'price'               => $data['price']             ?? 0,
                'weight'              => $data['weight']            ?? 0,
                'status'              => (int) ($data['status']     ?? 1),
                'short_description'   => $data['short_description'] ?? '',
                'description'         => $data['description']       ?? '',
            ];

            try {
                $product = $this->productRepository->create($productData);
                $product->vendor_id = $vendor->id;
                $product->save();
                $created++;
            } catch (\Throwable $e) {
                $errors[] = "SKU '{$data['sku']}': " . $e->getMessage();
            }
        }

        fclose($handle);

        $msg = "Importación: {$created} producto(s) creado(s).";
        if ($errors) {
            $msg .= ' Advertencias: ' . implode(' | ', array_slice($errors, 0, 5));
        }

        session()->flash('success', $msg);
        return redirect()->route('vendor.products.index');
    }
}

