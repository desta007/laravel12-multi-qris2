<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QRIS Payment Gateway</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold text-gray-900">QRIS Payment Gateway</h1>
            </div>
        </header>
        <main class="flex-grow">
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="px-4 py-6 sm:px-0">
                    <div class="border-4 border-dashed border-gray-200 rounded-lg p-4">
                        <h2 class="text-2xl font-semibold mb-4">Welcome to QRIS Payment Gateway</h2>
                        <p class="mb-6">Multi-bank QRIS payment solution for your business.</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                            <a href="/admin" class="block p-6 bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
                                <h3 class="text-xl font-semibold mb-2">Master Admin</h3>
                                <p class="text-gray-600">Manage all QRIS and system settings</p>
                            </a>
                            <a href="/admin" class="block p-6 bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
                                <h3 class="text-xl font-semibold mb-2">Admin</h3>
                                <p class="text-gray-600">Monitor transactions and member accounts</p>
                            </a>
                            <a href="/member" class="block p-6 bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
                                <h3 class="text-xl font-semibold mb-2">Member</h3>
                                <p class="text-gray-600">Access your dashboard and transactions</p>
                            </a>
                        </div>
                        
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-xl font-semibold mb-4">API Documentation</h3>
                            <p class="mb-4">Integrate QRIS payments into your application with our RESTful API.</p>
                            <a href="/docs" class="inline-block px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors">
                                View API Docs
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <footer class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
                <p class="text-center text-gray-500">© 2025 QRIS Payment Gateway. All rights reserved.</p>
            </div>
        </footer>
    </div>
</body>
</html>