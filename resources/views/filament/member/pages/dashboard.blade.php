<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6 dark:bg-gray-800">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Your Balance</h3>
                <span class="text-2xl font-bold text-green-600">Rp {{ number_format($this->balance->balance, 0, ',', '.') }}</span>
            </div>
            <div class="mt-4 grid grid-cols-2 gap-4">
                <div class="text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Income</p>
                    <p class="text-lg font-semibold text-green-600 dark:text-green-400">Rp {{ number_format($this->balance->total_income, 0, ',', '.') }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Expense</p>
                    <p class="text-lg font-semibold text-red-600 dark:text-red-400">Rp {{ number_format($this->balance->total_expense, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow p-6 dark:bg-gray-800">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Active QRIS</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach($this->activeQris as $qris)
                <div class="border rounded-lg p-4 dark:border-gray-700">
                    @if($qris->qris_image)
                        <div class="flex justify-center mb-2">
                            <img src="{{ asset('storage/' . $qris->qris_image) }}" alt="{{ $qris->name }}" class="w-32 h-32 object-contain">
                        </div>
                    @endif
                    <div class="flex items-center mb-2">
                        <svg class="w-5 h-5 text-blue-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 001 1zm0 10h2a1 1 0 001-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 001 1zM5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 001 1zm0 10h2a1 1 0 001-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                        </svg>
                        <h4 class="font-medium text-gray-900 dark:text-white">{{ $qris->name }}</h4>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $qris->bank->name ?? $qris->bank_name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">Fee: {{ $qris->fee_percentage }}%</p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow dark:bg-gray-800">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Recent Transactions</h3>
            @if($this->recentTransactions->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Transaction ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @foreach($this->recentTransactions as $transaction)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $transaction->transaction_id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($transaction->status == 'success') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                    @elseif($transaction->status == 'pending') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                    @else bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 @endif">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $transaction->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-gray-500 dark:text-gray-400 text-center py-4">No transactions found.</p>
            @endif
        </div>
    </div>
</x-filament-panels::page>