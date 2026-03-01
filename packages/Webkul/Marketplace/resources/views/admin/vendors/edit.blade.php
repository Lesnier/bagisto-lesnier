<x-admin::layouts>
    <x-slot:title>
        {{ __('marketplace::app.admin.vendors.edit-title') }}
    </x-slot>

    <form method="POST" action="{{ route('admin.marketplace.vendors.update', $vendor->id) }}" enctype="multipart/form-data">
        @csrf()
        @method('PUT')

        <div style="max-width: 900px; margin: 0 auto; padding: 24px; display: flex; flex-direction: column; gap: 24px;">
            
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
                <p style="font-size: 24px; font-weight: bold; display: flex; align-items: center; gap: 12px; margin: 0;" class="text-gray-800 dark:text-white">
                    <a href="{{ route('admin.marketplace.vendors.index') }}" class="icon-sort-left" style="font-size: 28px; cursor: pointer; text-decoration: none; color: inherit;"></a>
                    {{ __('marketplace::app.admin.vendors.edit-title') }}
                </p>

                <button type="submit" class="primary-button">
                    {{ __('marketplace::app.admin.vendors.save-btn') }}
                </button>
            </div>

            <!-- Shop Info Section -->
            <div class="bg-white dark:bg-gray-900" style="border-radius: 8px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); border: 1px solid #e5e7eb;">
                <p style="font-size: 18px; font-weight: 600; margin-bottom: 24px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb; margin-top: 0;" class="text-gray-900 dark:text-white dark:border-gray-700">
                    {{ __('marketplace::app.admin.vendors.shop-name') }} &amp; {{ __('marketplace::app.admin.vendors.descripcion') }}
                </p>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px;" class="text-gray-800 dark:text-gray-200" for="shop_name">
                        {{ __('marketplace::app.admin.vendors.shop-name') }} <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="text" name="shop_name" value="{{ old('shop_name') ?: $vendor->shop_name }}" required 
                        style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 10px 12px; font-size: 14px; background-color: transparent;" 
                        class="text-gray-900 dark:text-white dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div style="margin-bottom: 0;">
                    <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px;" class="text-gray-800 dark:text-gray-200" for="shop_description">
                        {{ __('marketplace::app.admin.vendors.descripcion') }}
                    </label>
                    <textarea name="shop_description" rows="5" 
                        style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 10px 12px; font-size: 14px; background-color: transparent;" 
                        class="text-gray-900 dark:text-white dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500">{{ old('shop_description') ?: $vendor->shop_description }}</textarea>
                </div>
            </div>

            <!-- Commission & Status Section -->
            <div class="bg-white dark:bg-gray-900" style="border-radius: 8px; padding: 24px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); border: 1px solid #e5e7eb;">
                <p style="font-size: 18px; font-weight: 600; margin-bottom: 24px; padding-bottom: 8px; border-bottom: 1px solid #e5e7eb; margin-top: 0;" class="text-gray-900 dark:text-white dark:border-gray-700">
                    {{ __('marketplace::app.admin.vendors.commission') }} &amp; {{ __('marketplace::app.admin.vendors.status') }}
                </p>

                <div style="margin-bottom: 20px;">
                    <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 8px;" class="text-gray-800 dark:text-gray-200" for="commission_percentage">
                        {{ __('marketplace::app.admin.vendors.commission') }} (%) <span style="color: #ef4444;">*</span>
                    </label>
                    <input type="number" step="0.01" name="commission_percentage" value="{{ old('commission_percentage') ?: $vendor->commission_percentage }}" required 
                        style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 10px 12px; font-size: 14px; background-color: transparent;"
                        class="text-gray-900 dark:text-white dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <div style="margin-bottom: 20px;">
                    <p style="display: flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 500; margin: 0;" class="text-gray-800 dark:text-gray-200">
                        {{ __('marketplace::app.admin.vendors.status') }}: 
                        @if($vendor->status === 'approved')
                            <span class="label-active" style="font-weight: bold; margin-left: 8px;">{{ ucfirst($vendor->status) }}</span>
                        @elseif($vendor->status === 'pending')
                            <span class="label-pending" style="font-weight: bold; margin-left: 8px;">{{ ucfirst($vendor->status) }}</span>
                        @else
                            <span class="label-canceled" style="font-weight: bold; margin-left: 8px;">{{ ucfirst($vendor->status) }}</span>
                        @endif
                    </p>
                </div>

                @if($vendor->status === 'pending' || $vendor->status === 'rejected')
                    <div style="margin-top: 24px; border-top: 1px solid #e5e7eb; padding-top: 24px;" class="dark:border-gray-700">
                        <button formaction="{{ route('admin.marketplace.vendors.approve', $vendor->id) }}" formmethod="POST" 
                                style="background-color: #16a34a; color: white; padding: 10px 24px; border-radius: 6px; font-weight: 500; font-size: 14px; border: none; cursor: pointer;">
                            {{ __('marketplace::app.admin.vendors.approve') }} Vendor
                        </button>
                        
                        <div style="background-color: #f9fafb; padding: 16px; border-radius: 8px; border: 1px solid #e5e7eb; margin-top: 16px;" class="dark:bg-gray-800 dark:border-gray-700">
                            <label style="display: block; font-size: 14px; font-weight: 500; margin-bottom: 12px;" class="text-gray-800 dark:text-white" for="rejection_reason">Razón de Rechazo (Sólo si rechazas)</label>
                            
                            <div style="display: flex; gap: 12px; align-items: stretch; flex-wrap: wrap; margin-bottom: 15px;">
                                <div style="flex: 1; min-width: 200px;">
                                    <input type="text" name="rejection_reason" placeholder="{{ __('marketplace::app.admin.vendors.reject-reason') }}" value="{{ old('rejection_reason') }}"
                                        style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 0px 12px; font-size: 14px; background-color: #ffffff; height: 100%; box-sizing: border-box;" 
                                        class="text-gray-900 dark:bg-gray-900 dark:text-white dark:border-gray-600 focus:ring-blue-500 focus:border-blue-500">
                                    @error('rejection_reason')
                                        <p style="color: #ef4444; font-size: 12px; margin-top: 4px; margin-bottom: 4px;">{{ $message }}</p>
                                    @enderror
                                </div>
                                
                                <button formaction="{{ route('admin.marketplace.vendors.reject', $vendor->id) }}" formmethod="POST" 
                                        style="background-color: #dc2626; color: white; padding: 10px 24px; border-radius: 6px; font-weight: 500; font-size: 14px; border: none; cursor: pointer; white-space: nowrap;">
                                    {{ __('marketplace::app.admin.vendors.reject') }} Vendor
                                </button>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </form>
</x-admin::layouts>
