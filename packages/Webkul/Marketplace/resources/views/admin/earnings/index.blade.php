<x-admin::layouts>
    <x-slot:title>
        {{ __('marketplace::app.admin.earnings.title') }}
    </x-slot>

    <div class="flex gap-4 justify-between items-center mb-6">
        <p class="text-xl text-gray-800 dark:text-white font-bold">
            {{ __('marketplace::app.admin.earnings.title') }}
        </p>

        <div class="flex gap-2">
            <a href="{{ route('admin.marketplace.earnings.index') }}" class="px-4 py-2 text-sm font-medium rounded-lg {{ !$status ? 'text-white bg-blue-600' : 'text-gray-900 bg-white border border-gray-200 hover:bg-gray-100 dark:bg-gray-800 dark:text-white dark:border-gray-700' }}">
                {{ __('marketplace::app.admin.earnings.all') }}
            </a>
            <a href="{{ route('admin.marketplace.earnings.index', ['status' => 'pending']) }}" class="px-4 py-2 text-sm font-medium rounded-lg {{ $status === 'pending' ? 'text-white bg-blue-600' : 'text-gray-900 bg-white border border-gray-200 hover:bg-gray-100 dark:bg-gray-800 dark:text-white dark:border-gray-700' }}">
                {{ __('marketplace::app.admin.earnings.pending') }}
            </a>
            <a href="{{ route('admin.marketplace.earnings.index', ['status' => 'paid']) }}" class="px-4 py-2 text-sm font-medium rounded-lg {{ $status === 'paid' ? 'text-white bg-blue-600' : 'text-gray-900 bg-white border border-gray-200 hover:bg-gray-100 dark:bg-gray-800 dark:text-white dark:border-gray-700' }}">
                {{ __('marketplace::app.admin.earnings.paid') }}
            </a>
        </div>
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
                        <th scope="col" class="px-6 py-4">{{ __('marketplace::app.admin.earnings.id') }}</th>
                        <th scope="col" class="px-6 py-4">{{ __('marketplace::app.admin.earnings.vendor') }}</th>
                        <th scope="col" class="px-6 py-4">{{ __('marketplace::app.admin.earnings.order_id') }}</th>
                        <th scope="col" class="px-6 py-4">{{ __('marketplace::app.admin.earnings.amount') }}</th>
                        <th scope="col" class="px-6 py-4">{{ __('marketplace::app.admin.earnings.status') }}</th>
                        <th scope="col" class="px-6 py-4 text-right">{{ __('marketplace::app.admin.earnings.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($earnings as $earning)
                        <tr class="border-b dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-950">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">#{{ $earning->id }}</td>
                            <td class="px-6 py-4 text-gray-900 dark:text-white">{{ $earning->vendor->shop_name }}</td>
                            <td class="px-6 py-4">#{{ $earning->order_id }}</td>
                            <td class="px-6 py-4">
                                <strong class="text-gray-900 dark:text-white">{{ core()->formatBasePrice($earning->vendor_amount) }}</strong>
                                <br><span class="text-xs text-gray-500">{{ __('marketplace::app.admin.earnings.commission') }}: {{ core()->formatBasePrice($earning->commission_amount) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @if($earning->status === 'paid')
                                    <span class="label-active">{{ ucfirst($earning->status) }}</span>
                                @elseif($earning->status === 'pending')
                                    <span class="label-pending">{{ ucfirst($earning->status) }}</span>
                                @else
                                    <span class="label-canceled">{{ ucfirst($earning->status) }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right">
                                @if($earning->status === 'pending')
                                    <form action="{{ route('admin.marketplace.earnings.pay', $earning->id) }}" method="POST" onsubmit="return confirm('{{ __('marketplace::app.admin.earnings.confirm-pay') }}');">
                                        @csrf
                                        <button type="submit" class="text-white bg-green-600 hover:bg-green-700 font-medium rounded-lg text-xs px-3 py-2">
                                            {{ __('marketplace::app.admin.earnings.mark-paid') }}
                                        </button>
                                    </form>
                                @else
                                    <span class="text-gray-400 text-sm">{{ $earning->paid_at ? $earning->paid_at->format('Y-m-d') : '—' }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-4 text-center">
                                {{ __('marketplace::app.admin.earnings.empty') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4">
            {{ $earnings->withQueryString()->links() }}
        </div>
    </div>
</x-admin::layouts>
