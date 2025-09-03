<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-4">
        <div class="bg-white rounded-lg shadow p-6 dark:bg-gray-800">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Distribution Strategy Settings</h2>
            
            <form wire:submit.prevent="save">
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Select Distribution Strategy
                        </label>
                        
                        <div class="space-y-4">
                            @foreach($this->strategies as $key => $label)
                            <div class="flex items-center">
                                <input
                                    type="radio"
                                    id="strategy_{{ $key }}"
                                    name="strategy"
                                    value="{{ $key }}"
                                    wire:model="strategy"
                                    class="h-4 w-4 text-primary-600 focus:ring-primary-500 border-gray-300 dark:bg-gray-700 dark:border-gray-600"
                                >
                                <label for="strategy_{{ $key }}" class="ml-3 block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    {{ $label }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-4">
                        <h3 class="text-lg font-medium text-blue-800 dark:text-blue-200 mb-2">Strategy Descriptions</h3>
                        <ul class="space-y-2 text-sm text-blue-700 dark:text-blue-300">
                            <li><strong>Random Selection:</strong> Randomly selects a QRIS for each transaction</li>
                            <li><strong>Round Robin Distribution:</strong> Distributes transactions evenly across all active QRIS</li>
                            <li><strong>Manual QRIS Selection:</strong> Allows manual selection of QRIS for each transaction</li>
                        </ul>
                    </div>
                    
                    <div class="flex justify-end">
                        <x-filament::button type="submit" color="primary">
                            Save Strategy
                        </x-filament::button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-filament-panels::page>