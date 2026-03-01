<x-admin::layouts>
    <x-slot:title>
        {{ __('marketplace::app.admin.vendors.title') }}
    </x-slot>

    <div class="flex gap-4 justify-between items-center mb-6">
        <p class="text-xl text-gray-800 dark:text-white font-bold">
            {{ __('marketplace::app.admin.vendors.title') }}
        </p>
    </div>

    @if(session('success'))
        <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white dark:bg-gray-900 relative shadow-sm sm:rounded-lg overflow-hidden border dark:border-gray-800">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-800 dark:text-gray-400 border-b dark:border-gray-700">
                    <tr>
                        <th scope="col" class="px-6 py-4">{{ __('marketplace::app.admin.vendors.id') }}</th>
                        <th scope="col" class="px-6 py-4">{{ __('marketplace::app.admin.vendors.shop-name') }}</th>
                        <th scope="col" class="px-6 py-4">{{ __('marketplace::app.admin.vendors.customer') }}</th>
                        <th scope="col" class="px-6 py-4">{{ __('marketplace::app.admin.vendors.status') }}</th>
                        <th scope="col" class="px-6 py-4 text-right">{{ __('marketplace::app.admin.vendors.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($vendors as $vendor)
                        <tr class="border-b dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-950">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $vendor->id }}</td>
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">{{ $vendor->shop_name }}</td>
                            <td class="px-6 py-4">{{ $vendor->customer->first_name }} {{ $vendor->customer->last_name }}</td>
                            <td class="px-6 py-4">
                                @if($vendor->status === 'approved')
                                    <span class="label-active">{{ ucfirst($vendor->status) }}</span>
                                @elseif($vendor->status === 'pending')
                                    <span class="label-pending">{{ ucfirst($vendor->status) }}</span>
                                @else
                                    <span class="label-canceled">{{ ucfirst($vendor->status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.marketplace.vendors.edit', $vendor->id) }}" class="text-blue-600 dark:text-blue-500 hover:underline">
                                    {{ __('marketplace::app.admin.vendors.edit') }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-4">
            {{ $vendors->links() }}
        </div>
    </div>
</x-admin::layouts>
