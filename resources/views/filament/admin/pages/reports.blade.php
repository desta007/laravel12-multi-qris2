<x-filament-panels::page>
    <div class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="startDate" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">Start Date</label>
                <input 
                    type="date" 
                    wire:model="startDate" 
                    id="startDate" 
                    class="w-full rounded-lg border border-gray-300 bg-white shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500"
                />
            </div>
            
            <div>
                <label for="endDate" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">End Date</label>
                <input 
                    type="date" 
                    wire:model="endDate" 
                    id="endDate" 
                    class="w-full rounded-lg border border-gray-300 bg-white shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500"
                />
            </div>
            
            <div>
                <label for="qrisId" class="block text-sm font-medium text-gray-700 mb-1 dark:text-gray-300">QRIS</label>
                <select 
                    wire:model="qrisId" 
                    id="qrisId" 
                    class="w-full rounded-lg border border-gray-300 bg-white shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:focus:border-primary-500"
                >
                    <option value="">All QRIS</option>
                    @foreach($this->qrisList as $qris)
                        <option value="{{ $qris->id }}">{{ $qris->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="flex items-end">
                <x-filament::button
                    wire:click="filter"
                    color="primary"
                >
                    Filter
                </x-filament::button>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow-md p-6 dark:bg-gray-800">
                <h3 class="text-lg font-semibold text-gray-700 mb-2 dark:text-gray-300">Total Transactions</h3>
                <p class="text-3xl font-bold text-primary-600 dark:text-primary-400">{{ $this->totalCount }}</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 dark:bg-gray-800">
                <h3 class="text-lg font-semibold text-gray-700 mb-2 dark:text-gray-300">Total Amount</h3>
                <p class="text-3xl font-bold text-success-600 dark:text-success-400">Rp {{ number_format($this->totalAmount, 0, ',', '.') }}</p>
            </div>
            
            <div class="bg-white rounded-lg shadow-md p-6 dark:bg-gray-800">
                <h3 class="text-lg font-semibold text-gray-700 mb-2 dark:text-gray-300">Total Fees</h3>
                <p class="text-3xl font-bold text-warning-600 dark:text-warning-400">Rp {{ number_format($this->totalFee, 0, ',', '.') }}</p>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden dark:bg-gray-800">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">Transaction List</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Transaction ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">User</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">QRIS</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Amount</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Fee</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                        @forelse($this->transactions as $transaction)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-200">{{ $transaction->transaction_id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $transaction->user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $transaction->qris->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">Rp {{ number_format($transaction->fee, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($transaction->status == 'success') bg-success-100 text-success-800 dark:bg-success-900 dark:text-success-200
                                    @elseif($transaction->status == 'pending') bg-warning-100 text-warning-800 dark:bg-warning-900 dark:text-warning-200
                                    @else bg-danger-100 text-danger-800 dark:bg-danger-900 dark:text-danger-200 @endif">
                                    {{ ucfirst($transaction->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $transaction->created_at->format('d M Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">No transactions found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament-panels::page>