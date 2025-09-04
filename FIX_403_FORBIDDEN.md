# Laravel QRIS Payment Gateway - Production 403 Forbidden Fix

## Problem
When trying to access the admin panel at `https://qris.dc-tech.web.id/admin/login`, after successful login, you get a 403 Forbidden error.

## Root Causes and Solutions

### 1. Role/Permission Mismatch

The most common cause is a mismatch between the roles assigned to users and the roles checked in the middleware.

#### Solution:
Update the user roles to match what's expected in the middleware.

1. Connect to your production database
2. Check existing roles:
   ```sql
   SELECT * FROM roles;
   ```

3. Check user roles:
   ```sql
   SELECT u.name, u.email, r.name as role 
   FROM users u 
   JOIN model_has_roles mhr ON u.id = mhr.model_id 
   JOIN roles r ON mhr.role_id = r.id;
   ```

4. If users don't have the correct roles, assign them:
   ```sql
   -- Assign master_admin role to your admin user
   INSERT INTO model_has_roles (role_id, model_type, model_id)
   SELECT r.id, 'App\\Models\\User', u.id
   FROM roles r, users u
   WHERE r.name = 'master_admin' AND u.email = 'your-admin-email@example.com'
   ON DUPLICATE KEY UPDATE role_id = role_id;
   ```

### 2. Implement FilamentUser Interface

The User model needs to implement the FilamentUser interface to properly handle panel access.

#### Solution:
Update your User model to implement the FilamentUser interface and add the required method.

1. Edit `app/Models/User.php`:
   ```php
   <?php
   
   namespace App\Models;
   
   use Filament\Models\Contracts\FilamentUser;
   use Filament\Panel;
   // ... other imports
   
   class User extends Authenticatable implements FilamentUser
   {
       // ... existing code
       
       public function canAccessPanel(Panel $panel): bool
       {
           if ($panel->getId() === 'admin') {
               return $this->hasAnyRole(['admin', 'master_admin']);
           }
           
           if ($panel->getId() === 'member') {
               return $this->hasRole('member');
           }
           
           return false;
       }
   }
   ```

### 3. Clear Permission Cache

Permission caching can cause issues in production.

#### Solution:
Clear the permission cache:

```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan permission:cache-reset
```

### 4. Verify Database Seeding

Ensure proper roles and permissions are seeded.

#### Solution:
Run the seeders:

```bash
php artisan db:seed --class=RolePermissionSeeder
```

Or if you need to reseed everything:
```bash
php artisan migrate:fresh --seed
```

## Step-by-Step Fix Process

### Step 1: Implement FilamentUser Interface
Add the `FilamentUser` interface and `canAccessPanel()` method to your User model.

### Step 2: Verify User Roles
Ensure your admin user has the 'master_admin' or 'admin' role.

### Step 3: Clear All Caches
Run all cache clearing commands to ensure fresh configuration.

### Step 4: Test Access
Try accessing the admin panel again.

## Alternative Solution: Update Middleware

If you prefer not to modify the User model, you can update the AdminRoleMiddleware:

Edit `app/Http/Middleware/AdminRoleMiddleware.php`:
```php
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminRoleMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('filament.admin.auth.login');
        }

        // Check if user has admin or master_admin role
        $user = Auth::user();
        
        // Debug: Log user roles
        \Log::info('User roles:', ['roles' => $user->getRoleNames()]);
        
        if (!$user->hasAnyRole(['admin', 'master_admin', 'super_admin', 'staff_admin'])) {
            \Log::warning('Unauthorized access attempt', [
                'user_id' => $user->id,
                'user_email' => $user->email
            ]);
            abort(403, 'Unauthorized access to admin panel.');
        }

        return $next($request);
    }
}
```

## Additional Checks

1. **File Permissions**: Ensure Laravel storage and bootstrap/cache directories are writable:
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

2. **Web Server Configuration**: Ensure your web server (Apache/Nginx) is properly configured to handle Laravel routing.

3. **Environment Variables**: Verify APP_ENV is set to 'production' and APP_DEBUG is set to 'false' in your .env file.

## Testing the Fix

After implementing the solutions:

1. Clear all caches
2. Try logging in to the admin panel
3. Check Laravel logs if issues persist:
   ```bash
   tail -f storage/logs/laravel.log
   ```

If you continue to experience issues, please share the error logs for further diagnosis.