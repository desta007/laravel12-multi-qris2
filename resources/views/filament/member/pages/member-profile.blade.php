<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-4">
        <div class="bg-white rounded-lg shadow p-6 dark:bg-gray-800">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Profile Information</h2>
                <a href="{{ route('filament.member.auth.profile') }}" class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Profile
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Personal Information</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ auth()->user()->email }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Phone</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ auth()->user()->phone ?? 'Not provided' }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Bank Information</h3>
                    <div class="space-y-4">
                        @if(auth()->user()->bank_name)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Bank Name</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ auth()->user()->bank_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Account Holder Name</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ auth()->user()->account_holder_name }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Account Number</label>
                            <p class="mt-1 text-sm text-gray-900 dark:text-white">{{ auth()->user()->account_number }}</p>
                        </div>
                        @else
                        <div class="text-center py-4">
                            <p class="text-gray-500 dark:text-gray-400 mb-4">No bank information registered yet.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>