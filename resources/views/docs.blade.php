<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation - QRIS Payment Gateway</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex flex-col">
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8 flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-900">API Documentation</h1>
                <a href="/" class="text-blue-600 hover:text-blue-800">← Back to Home</a>
            </div>
        </header>
        <main class="flex-grow">
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="px-4 py-6 sm:px-0">
                    <div class="border-4 border-dashed border-gray-200 rounded-lg p-4">
                        <h2 class="text-2xl font-semibold mb-4">QRIS Payment Gateway API</h2>
                        <p class="mb-6">This documentation provides information about the API endpoints available for integrating QRIS payments into your application.</p>
                        
                        <div class="bg-white p-6 rounded-lg shadow mb-6">
                            <h3 class="text-xl font-semibold mb-4">Authentication</h3>
                            <p class="mb-4">All API requests require authentication using an API token. Include the token in the Authorization header:</p>
                            <pre class="bg-gray-100 p-4 rounded mb-4">Authorization: Bearer YOUR_API_TOKEN</pre>
                        </div>
                        
                        <div class="bg-white p-6 rounded-lg shadow mb-6">
                            <h3 class="text-xl font-semibold mb-4">Generate QRIS Payment</h3>
                            <p class="mb-2"><strong>Endpoint:</strong> <code class="bg-gray-100 px-2 py-1 rounded">POST /api/payment/generate-qris</code></p>
                            <p class="mb-2"><strong>Description:</strong> Generates a QRIS code for payment</p>
                            <p class="mb-4"><strong>Parameters:</strong></p>
                            <ul class="list-disc pl-6 mb-4">
                                <li><strong>amount</strong> (required, numeric): Payment amount</li>
                                <li><strong>description</strong> (optional, string): Payment description</li>
                                <li><strong>callback_url</strong> (optional, url): URL to receive payment notifications</li>
                            </ul>
                            <p class="mb-2"><strong>Response:</strong></p>
                            <pre class="bg-gray-100 p-4 rounded">{
  "success": true,
  "data": {
    "transaction_id": "TRX-ABC123",
    "amount": 100000,
    "fee": 700,
    "total_amount": 100700,
    "qris_code": "DUMMY_QRIS_CODE_BCA_STATIC_001",
    "qris_image": null,
    "bank_name": "BCA",
    "expires_at": "2025-09-02T08:30:00.000000Z"
  }
}</pre>
                        </div>
                        
                        <div class="bg-white p-6 rounded-lg shadow mb-6">
                            <h3 class="text-xl font-semibold mb-4">Get Transaction Status</h3>
                            <p class="mb-2"><strong>Endpoint:</strong> <code class="bg-gray-100 px-2 py-1 rounded">GET /api/payment/transaction/{transactionId}</code></p>
                            <p class="mb-2"><strong>Description:</strong> Retrieves the status of a transaction</p>
                            <p class="mb-2"><strong>Response:</strong></p>
                            <pre class="bg-gray-100 p-4 rounded">{
  "success": true,
  "data": {
    "transaction_id": "TRX-ABC123",
    "amount": 100000,
    "fee": 700,
    "status": "success",
    "description": "Payment for order #123",
    "paid_at": "2025-09-02T08:25:00.000000Z",
    "created_at": "2025-09-02T08:20:00.000000Z"
  }
}</pre>
                        </div>
                        
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-xl font-semibold mb-4">Get QRIS List</h3>
                            <p class="mb-2"><strong>Endpoint:</strong> <code class="bg-gray-100 px-2 py-1 rounded">GET /api/payment/qris-list</code></p>
                            <p class="mb-2"><strong>Description:</strong> Retrieves the list of available QRIS</p>
                            <p class="mb-2"><strong>Response:</strong></p>
                            <pre class="bg-gray-100 p-4 rounded">{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "BCA Static QRIS",
      "bank_name": "BCA",
      "type": "static",
      "qris_image": null
    },
    {
      "id": 2,
      "name": "Mandiri Dynamic QRIS",
      "bank_name": "Mandiri",
      "type": "dynamic",
      "qris_image": null
    }
  ]
}</pre>
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