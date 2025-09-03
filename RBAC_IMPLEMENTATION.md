# Role-Based Access Control Implementation

## Overview
This document describes the role-based access control (RBAC) implementation for the QRIS Payment Gateway system.

## Roles and Permissions

### Master Admin
- **Full system access**
- Can manage all QRIS entries (create, read, update, delete)
- Can manage all transactions (create, read, update, delete)
- Can manage all member balances (create, read, update, delete)
- Can view and export reports
- Can access all Filament resources with full CRUD operations

### Admin
- **Limited management access**
- Can view and manage QRIS entries (create, read, update)
- Can view transactions (read-only)
- Can view member balances (read-only)
- Can view and export reports
- Cannot delete QRIS entries, transactions, or member balances
- Access to Filament resources with limited operations:
  - QRIS: Can create and edit, but NOT delete
  - Transactions: View only (no create, edit, or delete)
  - Member Balances: View only (no create, edit, or delete)

### Member
- **Dashboard and personal data access**
- Can view their own QRIS list
- Can view their own transaction history
- Can view their own balance
- Cannot access admin panel
- Cannot manage system settings

## Implementation Details

### 1. Permission System
Permissions are managed using the Spatie Permissions package:
- Permissions are defined in `RolePermissionSeeder`
- Roles are assigned specific permissions based on their access level
- Master Admin has all permissions
- Admin has a subset of permissions focused on monitoring
- Member has read-only access to their own data

### 2. Filament Resource Restrictions
Each Filament resource implements role-based access control:

#### QRIS Resource
- **Master Admin**: Full CRUD access
- **Admin**: Can create and edit, but cannot delete
- **Member**: Can only view (through member panel)

#### Transaction Resource
- **Master Admin**: Full CRUD access
- **Admin**: Read-only access (view only)
- **Member**: Can only view their own transactions (through member panel)

#### Member Balance Resource
- **Master Admin**: Full CRUD access
- **Admin**: Read-only access (view only)
- **Member**: Can only view their own balance (through member panel)

### 3. Report Access
- **Master Admin & Admin**: Can access reports and export data
- **Member**: No access to reports

### 4. Route-Level Protection
Routes are protected using middleware:
- Admin routes use `role:admin|master_admin` middleware
- Member routes use `role:member` middleware
- Each controller also implements role checking in constructors

## Testing Role Access

To test the role-based access control:

1. **Master Admin** (master@example.com / password):
   - Should have access to all admin features
   - Should be able to create, edit, and delete QRIS entries
   - Should be able to manage transactions and member balances

2. **Admin** (admin@example.com / password):
   - Should have access to admin dashboard
   - Should be able to view and create/edit QRIS entries
   - Should NOT be able to delete QRIS entries
   - Should only be able to view (not edit) transactions and member balances

3. **Member** (member@example.com / password):
   - Should only have access to member dashboard
   - Should NOT have access to admin panel
   - Should be able to view their own transactions and balance

## Code Implementation

Role checks are implemented in multiple places:

1. **Resource-level permissions** in Filament resources:
   - `canViewAny()`
   - `canCreate()`
   - `canEdit()`
   - `canDelete()`
   - `canDeleteAny()`

2. **Action-level restrictions** in table configurations:
   - Conditional display of edit/delete actions
   - Conditional bulk actions

3. **Controller-level middleware**:
   - Route middleware for role checking
   - Constructor-based role validation

4. **Page-level access**:
   - Separate view pages for read-only access
   - Action-specific pages for full access users