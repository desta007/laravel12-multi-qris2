<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel - QRIS Payment Gateway')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900">Admin Panel</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">{{ auth()->user()->name }}</span>
                    <a href="{{ route('logout') }}" class="text-red-600 hover:text-red-800">
                        Logout
                    </a>
                </div>
            </div>
        </header>
        
        <nav class="bg-gray-800 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex space-x-4">
                        <a href="{{ route('admin.reports.index') }}" class="px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('admin.reports.*') ? 'bg-gray-900' : 'hover:bg-gray-700' }}">Reports</a>
                    </div>
                </div>
            </div>
        </nav>
        
        <main class="flex-grow">
            @yield('content')
        </main>
        
        <footer class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
                <p class="text-center text-gray-500">© 2025 QRIS Payment Gateway. All rights reserved.</p>
            </div>
        </footer>
    </div>
</body>
</html>