<x-shop::layouts
    :has-header="true"
    :has-feature="true"
    :has-footer="true"
>
    <!-- Page Title -->
    <x-slot:title>
        {{ __('marketplace::app.shop.vendor.apply.title') }}
    </x-slot>

    <div class="container mt-20 max-1180:px-5 max-md:mt-12 mb-20">
        <!-- Form Container -->
        <div class="m-auto w-full max-w-[870px] rounded-xl border border-zinc-200 p-16 px-[90px] max-md:px-8 max-md:py-8 max-sm:border-none max-sm:p-0">
            <h1 class="font-dmserif text-4xl max-md:text-3xl max-sm:text-xl text-center">
                {{ __('marketplace::app.shop.vendor.apply.title') }}
            </h1>

            <p class="mt-4 text-xl text-zinc-500 max-sm:mt-0 max-sm:text-sm text-center">
                {{ __('marketplace::app.shop.vendor.apply.description') }}
            </p>

            <div class="mt-14 rounded max-sm:mt-8">
                <x-shop::form :action="route('marketplace.vendor.apply.store')">

                    <!-- Shop Name -->
                    <x-shop::form.control-group>
                        <x-shop::form.control-group.label class="required">
                            {{ __('marketplace::app.shop.vendor.apply.shop-name') }}
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control
                            type="text"
                            class="px-6 py-4 max-md:py-3 max-sm:py-2"
                            name="shop_name"
                            rules="required"
                            :value="old('shop_name')"
                            :label="trans('marketplace::app.shop.vendor.apply.shop-name')"
                            :placeholder="trans('marketplace::app.shop.vendor.apply.shop-name')"
                            aria-required="true"
                        />

                        <x-shop::form.control-group.error control-name="shop_name" />
                    </x-shop::form.control-group>

                    <!-- Shop Description -->
                    <x-shop::form.control-group>
                        <x-shop::form.control-group.label class="required">
                            {{ __('marketplace::app.shop.vendor.apply.shop-description') }}
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control
                            type="textarea"
                            class="px-6 py-4 max-md:py-3 max-sm:py-2"
                            name="shop_description"
                            rules="required"
                            :value="old('shop_description')"
                            :label="trans('marketplace::app.shop.vendor.apply.shop-description')"
                            :placeholder="trans('marketplace::app.shop.vendor.apply.shop-description')"
                            aria-required="true"
                        />

                        <x-shop::form.control-group.error control-name="shop_description" />
                    </x-shop::form.control-group>

                    <!-- Submit Button -->
                    <div class="mt-8 flex flex-wrap items-center gap-9 max-sm:justify-center max-sm:gap-5 max-sm:text-center">
                        <button
                            class="primary-button m-0 mx-auto block w-full max-w-[374px] rounded-2xl px-11 py-4 text-center text-base max-md:max-w-full max-md:rounded-lg max-md:py-3 max-sm:py-1.5 ltr:ml-0 rtl:mr-0"
                            type="submit"
                        >
                            {{ __('marketplace::app.shop.vendor.apply.submit-btn') }}
                        </button>
                    </div>

                </x-shop::form>
            </div>
        </div>
    </div>
</x-shop::layouts>
