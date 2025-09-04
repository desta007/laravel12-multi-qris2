<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Documentation - QRIS Payment Gateway</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                        <p class="mb-6">This documentation provides detailed information about the API endpoints available for integrating QRIS payments into your application.</p>
                        
                        <div class="bg-white p-6 rounded-lg shadow mb-6">
                            <h3 class="text-xl font-semibold mb-4">Authentication</h3>
                            <p class="mb-4">All protected API requests require authentication using Sanctum tokens. First, you need to authenticate using the login endpoint to obtain a token, then include the token in the Authorization header:</p>
                            <pre class="bg-gray-100 p-4 rounded mb-4">Authorization: Bearer YOUR_API_TOKEN</pre>
                            <p class="mb-2"><strong>To authenticate and get a token:</strong></p>
                            <pre class="bg-gray-100 p-4 rounded mb-4">POST /api/auth/login
Content-Type: application/json

{
    "email": "user@example.com",
    "password": "your_password"
}</pre>
                            <p class="mb-2"><strong>Response:</strong></p>
                            <pre class="bg-gray-100 p-4 rounded mb-4">{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "User Name",
      "email": "user@example.com"
    },
    "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
    "token_type": "Bearer"
  }
}</pre>
                        </div>
                        
                        <div class="bg-white p-6 rounded-lg shadow mb-6">
                            <h3 class="text-xl font-semibold mb-4">API Endpoints</h3>
                            
                            <!-- Authentication Endpoints -->
                            <div class="border-b border-gray-200 pb-4 mb-4">
                                <h4 class="text-lg font-semibold mb-2 flex items-center">
                                    <i class="fas fa-user-lock text-indigo-500 mr-2"></i> Authentication Endpoints
                                </h4>
                                
                                <div class="ml-4 mt-3">
                                    <h5 class="font-semibold mb-2">Login</h5>
                                    <p class="mb-2"><strong>Endpoint:</strong> <code class="bg-gray-100 px-2 py-1 rounded">POST /api/auth/login</code></p>
                                    <p class="mb-2"><strong>Description:</strong> Authenticate user and obtain access token</p>
                                    <p class="mb-2"><strong>Authentication:</strong> Not required</p>
                                    <p class="mb-2"><strong>Request Body:</strong></p>
                                    <pre class="bg-gray-100 p-4 rounded text-sm">{
  "email": "user@example.com",
  "password": "your_password"
}</pre>
                                    <p class="mb-2"><strong>Response:</strong></p>
                                    <pre class="bg-gray-100 p-4 rounded text-sm">{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "User Name",
      "email": "user@example.com"
    },
    "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
    "token_type": "Bearer"
  }
}</pre>
                                </div>
                                
                                <div class="ml-4 mt-4">
                                    <h5 class="font-semibold mb-2">Get Authenticated User</h5>
                                    <p class="mb-2"><strong>Endpoint:</strong> <code class="bg-gray-100 px-2 py-1 rounded">GET /api/auth/user</code></p>
                                    <p class="mb-2"><strong>Description:</strong> Get the authenticated user's information</p>
                                    <p class="mb-2"><strong>Authentication:</strong> Required</p>
                                    <p class="mb-2"><strong>Response:</strong></p>
                                    <pre class="bg-gray-100 p-4 rounded text-sm">{
  "success": true,
  "data": {
    "id": 1,
    "name": "User Name",
    "email": "user@example.com"
  }
}</pre>
                                </div>
                                
                                <div class="ml-4 mt-4">
                                    <h5 class="font-semibold mb-2">Logout</h5>
                                    <p class="mb-2"><strong>Endpoint:</strong> <code class="bg-gray-100 px-2 py-1 rounded">POST /api/auth/logout</code></p>
                                    <p class="mb-2"><strong>Description:</strong> Revoke the current access token</p>
                                    <p class="mb-2"><strong>Authentication:</strong> Required</p>
                                    <p class="mb-2"><strong>Response:</strong></p>
                                    <pre class="bg-gray-100 p-4 rounded text-sm">{
  "success": true,
  "message": "Logged out successfully"
}</pre>
                                </div>
                            </div>
                            
                            <!-- Test API Endpoint -->
                            <div class="border-b border-gray-200 pb-4 mb-4">
                                <h4 class="text-lg font-semibold mb-2 flex items-center">
                                    <i class="fas fa-vial text-blue-500 mr-2"></i> Test API Connection
                                </h4>
                                <p class="mb-2"><strong>Endpoint:</strong> <code class="bg-gray-100 px-2 py-1 rounded">GET /api/test-api</code></p>
                                <p class="mb-2"><strong>Description:</strong> Test if the API is working</p>
                                <p class="mb-2"><strong>Authentication:</strong> Not required</p>
                                <p class="mb-2"><strong>Response:</strong></p>
                                <pre class="bg-gray-100 p-4 rounded text-sm">{
  "success": true,
  "message": "QRIS Payment Gateway API is working!",
  "timestamp": "2025-09-03T10:30:00.000000Z"
}</pre>
                            </div>
                            
                            <!-- Generate QRIS Payment -->
                            <div class="border-b border-gray-200 pb-4 mb-4">
                                <h4 class="text-lg font-semibold mb-2 flex items-center">
                                    <i class="fas fa-qrcode text-green-500 mr-2"></i> Generate QRIS Payment
                                </h4>
                                <p class="mb-2"><strong>Endpoint:</strong> <code class="bg-gray-100 px-2 py-1 rounded">POST /api/payment/generate-qris</code></p>
                                <p class="mb-2"><strong>Description:</strong> Generates a QRIS code for payment</p>
                                <p class="mb-2"><strong>Authentication:</strong> Required</p>
                                <p class="mb-2"><strong>Request Body:</strong></p>
                                <pre class="bg-gray-100 p-4 rounded text-sm">{
  "amount": 100000,
  "description": "Payment for order #123",
  "callback_url": "https://yourapp.com/payment/callback",
  "distribution_strategy": "random", // Optional: random, round_robin, specific
  "qris_id": 1 // Required only if distribution_strategy is "specific"
}</pre>
                                <p class="mb-2"><strong>Parameters:</strong></p>
                                <ul class="list-disc pl-6 mb-2">
                                    <li><strong>amount</strong> (required, numeric): Payment amount (minimum 1000)</li>
                                    <li><strong>description</strong> (optional, string): Payment description (max 255 characters)</li>
                                    <li><strong>callback_url</strong> (optional, url): URL to receive payment notifications</li>
                                    <li><strong>distribution_strategy</strong> (optional, enum): QRIS selection strategy
                                        <ul class="list-circle pl-6 mt-1">
                                            <li><code>random</code> - Select a random active QRIS (default)</li>
                                            <li><code>round_robin</code> - Distribute payments evenly across QRIS</li>
                                            <li><code>specific</code> - Use a specific QRIS (requires qris_id)</li>
                                        </ul>
                                    </li>
                                    <li><strong>qris_id</strong> (conditional, integer): Specific QRIS ID (required only if distribution_strategy is "specific")</li>
                                </ul>
                                <p class="mb-2"><strong>Response:</strong></p>
                                <pre class="bg-gray-100 p-4 rounded text-sm">{
  "success": true,
  "data": {
    "transaction_id": "TRX-ABC123XYZ7",
    "amount": 100000,
    "fee": 700,
    "total_amount": 100700,
    "qris_code": "DUMMY_QRIS_CODE_BCA_STATIC_001",
    "qris_image": null,
    "bank_name": "BCA",
    "expires_at": "2025-09-03T10:45:00.000000Z"
  }
}</pre>
                            </div>
                            
                            <!-- Get Transaction Status -->
                            <div class="border-b border-gray-200 pb-4 mb-4">
                                <h4 class="text-lg font-semibold mb-2 flex items-center">
                                    <i class="fas fa-search text-yellow-500 mr-2"></i> Get Transaction Status
                                </h4>
                                <p class="mb-2"><strong>Endpoint:</strong> <code class="bg-gray-100 px-2 py-1 rounded">GET /api/payment/transaction/{transactionId}</code></p>
                                <p class="mb-2"><strong>Description:</strong> Retrieves the status of a transaction</p>
                                <p class="mb-2"><strong>Authentication:</strong> Required</p>
                                <p class="mb-2"><strong>Path Parameters:</strong></p>
                                <ul class="list-disc pl-6 mb-2">
                                    <li><strong>transactionId</strong> (required, string): The transaction ID returned when generating QRIS</li>
                                </ul>
                                <p class="mb-2"><strong>Response:</strong></p>
                                <pre class="bg-gray-100 p-4 rounded text-sm">{
  "success": true,
  "data": {
    "transaction_id": "TRX-ABC123XYZ7",
    "amount": 100000,
    "fee": 700,
    "status": "success", // pending, success, failed, expired
    "description": "Payment for order #123",
    "paid_at": "2025-09-03T10:25:00.000000Z",
    "created_at": "2025-09-03T10:20:00.000000Z"
  }
}</pre>
                            </div>
                            
                            <!-- Get QRIS List -->
                            <div class="border-b border-gray-200 pb-4 mb-4">
                                <h4 class="text-lg font-semibold mb-2 flex items-center">
                                    <i class="fas fa-list text-purple-500 mr-2"></i> Get QRIS List
                                </h4>
                                <p class="mb-2"><strong>Endpoint:</strong> <code class="bg-gray-100 px-2 py-1 rounded">GET /api/payment/qris-list</code></p>
                                <p class="mb-2"><strong>Description:</strong> Retrieves the list of available active QRIS</p>
                                <p class="mb-2"><strong>Authentication:</strong> Required</p>
                                <p class="mb-2"><strong>Response:</strong></p>
                                <pre class="bg-gray-100 p-4 rounded text-sm">{
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
                            
                            <!-- Handle Callback -->
                            <div class="border-b border-gray-200 pb-4 mb-4">
                                <h4 class="text-lg font-semibold mb-2 flex items-center">
                                    <i class="fas fa-bell text-red-500 mr-2"></i> Handle Payment Callback
                                </h4>
                                <p class="mb-2"><strong>Endpoint:</strong> <code class="bg-gray-100 px-2 py-1 rounded">POST /api/payment/callback</code></p>
                                <p class="mb-2"><strong>Description:</strong> Receives payment notifications from the payment gateway</p>
                                <p class="mb-2"><strong>Authentication:</strong> Not required (public endpoint)</p>
                                <p class="mb-2"><strong>Request Body:</strong></p>
                                <pre class="bg-gray-100 p-4 rounded text-sm">{
  "transaction_id": "TRX-ABC123XYZ7",
  "status": "success",
  "amount": 100000,
  "timestamp": "2025-09-03T10:25:00.000000Z"
}</pre>
                                <p class="mb-2"><strong>Response:</strong></p>
                                <pre class="bg-gray-100 p-4 rounded text-sm">{
  "success": true,
  "message": "Payment processed successfully"
}</pre>
                            </div>
                            
                            <!-- Admin Endpoints -->
                            <div class="border-b border-gray-200 pb-4 mb-4">
                                <h4 class="text-lg font-semibold mb-2 flex items-center">
                                    <i class="fas fa-user-shield text-orange-500 mr-2"></i> Admin Endpoints
                                </h4>
                                
                                <div class="ml-4 mt-3">
                                    <h5 class="font-semibold mb-2">Get Reports</h5>
                                    <p class="mb-2"><strong>Endpoint:</strong> <code class="bg-gray-100 px-2 py-1 rounded">GET /api/admin/reports</code></p>
                                    <p class="mb-2"><strong>Description:</strong> Get reports data (admin users only)</p>
                                    <p class="mb-2"><strong>Authentication:</strong> Required (admin privileges)</p>
                                    <p class="mb-2"><strong>Response:</strong></p>
                                    <pre class="bg-gray-100 p-4 rounded text-sm">{
  "message": "Reports page"
}</pre>
                                </div>
                                
                                <div class="ml-4 mt-4">
                                    <h5 class="font-semibold mb-2">Export Reports</h5>
                                    <p class="mb-2"><strong>Endpoint:</strong> <code class="bg-gray-100 px-2 py-1 rounded">GET /api/admin/reports/export</code></p>
                                    <p class="mb-2"><strong>Description:</strong> Export reports data (admin users only)</p>
                                    <p class="mb-2"><strong>Authentication:</strong> Required (admin privileges)</p>
                                    <p class="mb-2"><strong>Response:</strong></p>
                                    <pre class="bg-gray-100 p-4 rounded text-sm">{
  "message": "Export reports"
}</pre>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white p-6 rounded-lg shadow mb-6">
                            <h3 class="text-xl font-semibold mb-4">Distribution Strategies</h3>
                            <p class="mb-4">The QRIS Payment Gateway supports multiple distribution strategies for selecting which QRIS to use for a payment:</p>
                            
                            <div class="mb-4">
                                <h4 class="text-lg font-semibold mb-2">1. Random Distribution</h4>
                                <p class="mb-2">Selects a random active QRIS for each payment request. This is the default strategy.</p>
                                <pre class="bg-gray-100 p-4 rounded text-sm">{
  "distribution_strategy": "random"
}</pre>
                            </div>
                            
                            <div class="mb-4">
                                <h4 class="text-lg font-semibold mb-2">2. Round Robin Distribution</h4>
                                <p class="mb-2">Distributes payments evenly across all active QRIS in a rotating fashion.</p>
                                <pre class="bg-gray-100 p-4 rounded text-sm">{
  "distribution_strategy": "round_robin"
}</pre>
                            </div>
                            
                            <div class="mb-4">
                                <h4 class="text-lg font-semibold mb-2">3. Specific QRIS</h4>
                                <p class="mb-2">Uses a specific QRIS identified by its ID.</p>
                                <pre class="bg-gray-100 p-4 rounded text-sm">{
  "distribution_strategy": "specific",
  "qris_id": 1
}</pre>
                            </div>
                            
                            <p class="mb-2"><strong>Note:</strong> The default distribution strategy can be configured in the <code>config/qris.php</code> file or through the <code>QRIS_DEFAULT_DISTRIBUTION_STRATEGY</code> environment variable.</p>
                        </div>
                        
                        <div class="bg-white p-6 rounded-lg shadow mb-6">
                            <h3 class="text-xl font-semibold mb-4">Error Responses</h3>
                            <p class="mb-4">All API endpoints return consistent error responses in the following format:</p>
                            <pre class="bg-gray-100 p-4 rounded text-sm">{
  "success": false,
  "message": "Error description"
}</pre>
                            <p class="mb-2"><strong>Common HTTP Status Codes:</strong></p>
                            <ul class="list-disc pl-6 mb-2">
                                <li><strong>400 Bad Request</strong> - Invalid request parameters</li>
                                <li><strong>401 Unauthorized</strong> - Missing or invalid authentication token</li>
                                <li><strong>403 Forbidden</strong> - Insufficient permissions (e.g., accessing admin endpoints without admin role)</li>
                                <li><strong>404 Not Found</strong> - Resource not found (e.g., transaction ID)</li>
                                <li><strong>500 Internal Server Error</strong> - Server-side errors</li>
                            </ul>
                        </div>
                        
                        <div class="bg-white p-6 rounded-lg shadow mb-6">
                            <h3 class="text-xl font-semibold mb-4">Rate Limiting</h3>
                            <p class="mb-4">The API implements rate limiting to prevent abuse. Excessive requests may result in temporary blocks.</p>
                        </div>
                        
                        <div class="bg-white p-6 rounded-lg shadow">
                            <h3 class="text-xl font-semibold mb-4">Usage Examples</h3>
                            
                            <div class="mb-4">
                                <h4 class="text-lg font-semibold mb-2">1. Authenticate and Generate a Payment QRIS</h4>
                                <pre class="bg-gray-100 p-4 rounded text-sm"># First, authenticate to get a token
curl -X POST https://yourdomain.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "your_password"
  }'

# Use the token to generate a payment QRIS
curl -X POST https://yourdomain.com/api/payment/generate-qris \
  -H "Authorization: Bearer YOUR_API_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 50000,
    "description": "Product purchase",
    "callback_url": "https://yourapp.com/webhook"
  }'</pre>
                            </div>
                            
                            <div class="mb-4">
                                <h4 class="text-lg font-semibold mb-2">2. Check Transaction Status</h4>
                                <pre class="bg-gray-100 p-4 rounded text-sm">curl -X GET https://yourdomain.com/api/payment/transaction/TRX-ABC123XYZ7 \
  -H "Authorization: Bearer YOUR_API_TOKEN"</pre>
                            </div>
                            
                            <div class="mb-4">
                                <h4 class="text-lg font-semibold mb-2">3. Get Available QRIS List</h4>
                                <pre class="bg-gray-100 p-4 rounded text-sm">curl -X GET https://yourdomain.com/api/payment/qris-list \
  -H "Authorization: Bearer YOUR_API_TOKEN"</pre>
                            </div>
                            
                            <div>
                                <h4 class="text-lg font-semibold mb-2">4. Handle Payment Callback (Server-side)</h4>
                                <pre class="bg-gray-100 p-4 rounded text-sm">// This endpoint is called by the payment gateway
// Your server should handle this to update order status
curl -X POST https://yourdomain.com/api/payment/callback \
  -H "Content-Type: application/json" \
  -d '{
    "transaction_id": "TRX-ABC123XYZ7",
    "status": "success",
    "amount": 50000
  }'</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <footer class="bg-white shadow mt-8">
            <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
                <p class="text-center text-gray-500">© 2025 QRIS Payment Gateway. All rights reserved.</p>
            </div>
        </footer>
    </div>
</body>
</html>