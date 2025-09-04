# QRIS Payment Gateway - Simulation Guide

This document provides instructions on how to simulate QRIS payments using the dummy/sandbox environment before activating real QRIS codes.

## Overview

The QRIS Payment Gateway system includes built-in tools for simulating payments during development and testing. These tools allow you to:

1. Generate test transactions
2. Simulate payment callbacks
3. Verify transaction processing
4. Test balance updates

## Prerequisites

Before running simulations, ensure you have:

1. Installed and configured the application
2. Run database migrations and seeders:
   ```bash
   php artisan migrate --seed
   ```
3. At least one active QRIS entry in the database (provided by QrisSeeder)

## Simulation Methods

### 1. Console Command Simulation

The system provides a console command to simulate payments:

```bash
php artisan app:simulate-payment {user?} {amount?}
```

#### Parameters:
- `user` (optional): User ID for the transaction. If not provided, a random member will be selected.
- `amount` (optional): Transaction amount in IDR. If not provided, a random amount between 10,000 and 1,000,000 will be used.

#### Examples:

Create a random payment simulation:
```bash
php artisan app:simulate-payment
```

Create a payment simulation for user ID 3 with amount 50000:
```bash
php artisan app:simulate-payment 3 50000
```

### 2. API Endpoint Simulation

You can also simulate payments using the API endpoints:

#### Generate Payment QRIS
```bash
POST /api/payment/generate-qris
```

Headers:
```
Authorization: Bearer {token}
Content-Type: application/json
```

Body:
```json
{
  "amount": 100000,
  "description": "Test payment",
  "callback_url": "https://your-merchant-site.com/callback"
}
```

#### Get Transaction Status
```bash
GET /api/payment/transaction/{transaction_id}
```

Headers:
```
Authorization: Bearer {token}
```

### 3. Manual Callback Simulation

To simulate a payment callback (when a user completes payment), send a POST request to:

```bash
POST /api/payment/callback
```

Body:
```json
{
  "transaction_id": "TRX-RANDOMSTRING",
  "status": "success",
  "paid_at": "2023-06-15T10:30:00Z"
}
```

## Sample Simulation Workflow

### Step 1: Create a Test Transaction

Run the simulation command:
```bash
php artisan app:simulate-payment
```

Expected output:
```
Created transaction: TRX-ABC123XYZ
User: John Doe
Amount: Rp 500.000
Fee: Rp 3.500
QRIS: BCA Static QRIS
```

### Step 2: Check Transaction Status

Using the transaction ID from step 1:
```bash
curl -H "Authorization: Bearer {token}" \
http://localhost:8000/api/payment/transaction/TRX-ABC123XYZ
```

Response:
```json
{
  "success": true,
  "data": {
    "transaction_id": "TRX-ABC123XYZ",
    "amount": 500000,
    "fee": 3500,
    "status": "pending",
    "description": "Simulated payment for testing",
    "paid_at": null,
    "created_at": "2023-06-15T10:00:00Z"
  }
}
```

### Step 3: Simulate Payment Completion

Send a callback to simulate payment completion:
```bash
curl -X POST \
-H "Content-Type: application/json" \
-d '{"transaction_id":"TRX-ABC123XYZ","status":"success"}' \
http://localhost:8000/api/payment/callback
```

Response:
```json
{
  "success": true,
  "message": "Payment processed successfully"
}
```

### Step 4: Verify Transaction Status

Check the transaction status again:
```bash
curl -H "Authorization: Bearer {token}" \
http://localhost:8000/api/payment/transaction/TRX-ABC123XYZ
```

Response:
```json
{
  "success": true,
  "data": {
    "transaction_id": "TRX-ABC123XYZ",
    "amount": 500000,
    "fee": 3500,
    "status": "success",
    "description": "Simulated payment for testing",
    "paid_at": "2023-06-15T10:30:00Z",
    "created_at": "2023-06-15T10:00:00Z"
  }
}
```

## Dummy Data

The system includes the following dummy QRIS entries after seeding:

1. BCA Static QRIS (Fee: 0.7%)
2. Mandiri Dynamic QRIS (Fee: 0.8%)
3. BNI Static QRIS (Fee: 0.6%)
4. BRI Dynamic QRIS (Fee: 0.75%)

These QRIS entries use dummy codes and can be used for simulation purposes.

## Testing Different Scenarios

### Test Transaction Expiration

1. Create a transaction
2. Wait for 15 minutes (default expiration time)
3. Attempt to process payment - should fail

### Test Different Distribution Strategies

The system supports three QRIS distribution strategies:

1. Random (default)
2. Round-robin
3. Specific

To test different strategies, use the API with the `distribution_strategy` parameter:

```json
{
  "amount": 100000,
  "distribution_strategy": "round_robin"
}
```

### Test Fee Calculations

Different QRIS entries have different fee percentages. Verify that fees are calculated correctly based on the QRIS used.

## Verification Points

After simulation, verify the following:

1. Transaction status updated to "success"
2. Member balance increased by transaction amount
3. Callback sent to merchant URL (if provided)
4. Transaction appears in reports

## Resetting Simulation Data

To reset simulation data:

1. Clear transaction data:
   ```bash
   php artisan migrate:fresh --seed
   ```

2. Reset round-robin index (if needed):
   ```bash
   php artisan tinker
   >>> \App\Services\QrisDistributionService::resetRoundRobinIndex()
   ```

## Security Notes

1. Simulation tools are designed for development and testing environments only
2. Never use real payment data in simulation
3. Ensure proper authentication when using API endpoints
4. Callback endpoints should verify the source in production environments

## Troubleshooting

### No Active QRIS Found
Ensure database seeders have been run:
```bash
php artisan db:seed
```

### User Not Found
Ensure user accounts exist in the database:
```bash
php artisan db:seed --class=UserSeeder
```

### Permission Denied
Ensure API requests include valid authentication tokens.

## Additional Resources

1. [API Documentation](/docs)
2. [Implementation Summary](IMPLEMENTATION_SUMMARY.md)
3. [QRIS System Overview](README_QRIS.md)