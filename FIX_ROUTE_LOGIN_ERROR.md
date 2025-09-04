# Fix for "Route [login] not defined" Error in QRIS Payment Gateway API

## Problem
When making API requests to authenticated endpoints like `/api/payment/generate-qris`, you receive the error:
```
InvalidArgumentException: Route [login] not defined.
```

## Root Cause
This error occurs because the default Laravel authentication middleware (`auth:sanctum`) tries to redirect unauthenticated users to a login page when they make requests to protected routes. However, for API requests, this behavior is inappropriate as APIs should return JSON responses rather than HTTP redirects.

The issue specifically happens because:
1. The middleware attempts to redirect to a route named "login"
2. No such named route exists in the application
3. API clients (like Postman) expect JSON responses, not redirects

## Solution
We've implemented a custom solution that includes:

### 1. Custom Exception Handler
Created `app/Exceptions/Handler.php` with a custom `unauthenticated` method that returns JSON responses for API requests:

```php
protected function unauthenticated($request, AuthenticationException $exception)
{
    // For API requests, return JSON response instead of redirecting to login
    if ($request->expectsJson() || $request->is('api/*')) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthenticated.'
        ], 401);
    }

    // For web requests, redirect to login
    return redirect()->guest(route('login'));
}
```

### 2. Custom Authentication Middleware
Created `app/Http/Middleware/ApiAuthenticate.php` that properly handles authentication for API requests:

```php
public function handle(Request $request, Closure $next): Response
{
    // Check if this is an API request
    if ($request->is('api/*')) {
        // For API requests, we want to return JSON responses for auth failures
        try {
            // Use the default auth middleware but catch any authentication exceptions
            $response = app(\Illuminate\Auth\Middleware\Authenticate::class)->handle($request, function ($request) use ($next) {
                return $next($request);
            }, 'sanctum');
            
            return $response;
        } catch (AuthenticationException $e) {
            // Return JSON response for API authentication failures
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.'
            ], 401);
        }
    }
    
    // For non-API requests, use the default auth middleware
    return app(\Illuminate\Auth\Middleware\Authenticate::class)->handle($request, $next, 'sanctum');
}
```

### 3. Updated API Routes
Modified `routes/api.php` to use the custom middleware:

```php
// API routes
Route::middleware(ApiAuthenticate::class)->group(function () {
    Route::post('/payment/generate-qris', [PaymentController::class, 'generateQris']);
    Route::get('/payment/transaction/{transactionId}', [PaymentController::class, 'getTransactionStatus']);
    Route::get('/payment/qris-list', [PaymentController::class, 'getQrisList']);
});

// Admin routes
Route::middleware([ApiAuthenticate::class, 'role:admin|master_admin'])->group(function () {
    Route::get('/admin/reports', [ReportController::class, 'index'])->name('admin.reports.index');
    Route::get('/admin/reports/export', [ReportController::class, 'export'])->name('admin.reports.export');
});
```

## Implementation Steps

### Step 1: Create Required Files
1. Create the custom exception handler at `app/Exceptions/Handler.php`
2. Create the custom middleware at `app/Http/Middleware/ApiAuthenticate.php`

### Step 2: Update Routes
Update `routes/api.php` to use the new middleware as shown above.

### Step 3: Publish Sanctum Configuration (if not already done)
```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
```

### Step 4: Test the Fix
1. Try accessing an authenticated API endpoint without a token - you should now get a 401 JSON response
2. Obtain a valid token and try again - the request should succeed

## Usage with Postman

After implementing this fix, you can use the API with Postman as follows:

1. Make sure your `auth_token` environment variable is set to a valid Sanctum token
2. All authenticated endpoints will now properly return 401 errors for unauthenticated requests
3. Authenticated requests will work as expected

## Verification

To verify the fix is working:

1. Make a request to `/api/payment/generate-qris` without authentication:
   ```bash
   curl -X POST http://localhost:8000/api/payment/generate-qris
   ```
   You should receive:
   ```json
   {
     "success": false,
     "message": "Unauthenticated."
   }
   ```

2. Make the same request with a valid token:
   ```bash
   curl -X POST http://localhost:8000/api/payment/generate-qris \
     -H "Authorization: Bearer YOUR_VALID_TOKEN" \
     -H "Content-Type: application/json" \
     -d '{"amount": 100000}'
   ```
   You should receive a successful response with QRIS data.

## Additional Notes

1. This fix maintains backward compatibility with web routes
2. The solution follows Laravel best practices for API authentication
3. Error responses are now consistent across all API endpoints
4. The fix works for both regular authenticated endpoints and admin-restricted endpoints