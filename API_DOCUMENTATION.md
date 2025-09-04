# QRIS Payment Gateway API Documentation

## Overview
This document provides comprehensive documentation for the QRIS Payment Gateway API endpoints. The API allows integration with the QRIS payment system for generating payment QR codes, checking transaction status, and handling payment callbacks.

## Authentication
Most API endpoints require authentication using Laravel Sanctum tokens. To authenticate:

1. Use the `/api/auth/login` endpoint with valid credentials
2. Include the returned token in the `Authorization` header for subsequent requests
3. Format: `Authorization: Bearer {token}`

## Base URL
All endpoints are prefixed with `/api`. For example: `https://your-domain.com/api/auth/login`

## API Endpoints

### Authentication Endpoints

#### POST /api/auth/login
Authenticate a user and obtain an access token.

**Request Body:**
```json
{
  "email": "string (required)",
  "password": "string (required)"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "user": {
      "id": 1,
      "name": "User Name",
      "email": "user@example.com",
      // ... other user fields
    },
    "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
    "token_type": "Bearer"
  }
}
```

**Error Response:**
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password field is required."]
  }
}
```

#### GET /api/auth/user
Get the authenticated user's information.

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "User Name",
    "email": "user@example.com",
    // ... other user fields
  }
}
```

#### POST /api/auth/logout
Revoke the current access token.

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

### Public Endpoints

#### GET /api/test-api
Test if the API is working.

**Response:**
```json
{
  "success": true,
  "message": "QRIS Payment Gateway API is working!",
  "timestamp": "2023-06-15T10:30:00.000000Z"
}
```

#### POST /api/payment/callback
Handle payment notifications from QRIS providers.

**Request Body:**
```json
{
  "transaction_id": "string (required)",
  "status": "string (required)",
  "paid_at": "datetime (optional)"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Payment processed successfully"
}
```

### Authenticated Endpoints

#### POST /api/payment/generate-qris
Generate a new payment QRIS for a transaction.

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
  "amount": "numeric (required, minimum: 1000)",
  "description": "string (optional)",
  "callback_url": "url (optional)",
  "distribution_strategy": "string (optional, values: random, round_robin, specific)",
  "qris_id": "integer (optional, required when strategy is 'specific')"
}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "transaction_id": "TRX-ABC123XYZ",
    "amount": 100000,
    "fee": 700,
    "total_amount": 100700,
    "qris_code": "DUMMY_QRIS_CODE_BCA_STATIC_001",
    "qris_image": null,
    "bank_name": "BCA",
    "expires_at": "2023-06-15T10:45:00.000000Z"
  }
}
```

#### GET /api/payment/transaction/{transactionId}
Get the status of a specific transaction.

**Headers:**
```
Authorization: Bearer {token}
```

**Parameters:**
- `transactionId`: The transaction identifier

**Response:**
```json
{
  "success": true,
  "data": {
    "transaction_id": "TRX-ABC123XYZ",
    "amount": 100000,
    "fee": 700,
    "status": "pending",
    "description": "Test payment",
    "paid_at": null,
    "created_at": "2023-06-15T10:30:00.000000Z"
  }
}
```

#### GET /api/payment/qris-list
Get list of active QRIS.

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "BCA Static QRIS",
      "bank_name": "BCA",
      "type": "static",
      "qris_image": null
    }
  ]
}
```

### Admin Endpoints

#### GET /api/admin/reports
Get reports data (admin users only).

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "message": "Reports page"
}
```

#### GET /api/admin/reports/export
Export reports data (admin users only).

**Headers:**
```
Authorization: Bearer {token}
```

**Response:**
```json
{
  "message": "Export reports"
}
```

## Error Responses

### 401 Unauthorized
```json
{
  "success": false,
  "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
  "success": false,
  "message": "Unauthorized access to admin panel."
}
```

### 404 Not Found
```json
{
  "success": false,
  "message": "Transaction not found"
}
```

### 422 Validation Error
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "amount": ["The amount field is required."],
    "amount": ["The amount must be at least 1000."]
  }
}
```

## Usage Examples

### 1. Authentication Flow
```bash
# Login to get a token
curl -X POST https://your-domain.com/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "user@example.com",
    "password": "password"
  }'

# Use the token for authenticated requests
curl -X GET https://your-domain.com/api/auth/user \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### 2. Generate Payment QRIS
```bash
curl -X POST https://your-domain.com/api/payment/generate-qris \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -H "Content-Type: application/json" \
  -d '{
    "amount": 100000,
    "description": "Test payment",
    "callback_url": "https://your-merchant-site.com/callback"
  }'
```

### 3. Check Transaction Status
```bash
curl -X GET https://your-domain.com/api/payment/transaction/TRX-ABC123XYZ \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### 4. Handle Payment Callback
```bash
curl -X POST https://your-domain.com/api/payment/callback \
  -H "Content-Type: application/json" \
  -d '{
    "transaction_id": "TRX-ABC123XYZ",
    "status": "success",
    "paid_at": "2023-06-15T10:35:00Z"
  }'
```

## Security Considerations

1. Always use HTTPS in production environments
2. Store tokens securely and never expose them in client-side code
3. Implement proper token expiration and rotation
4. Validate all input data on the server side
5. Use appropriate role-based access controls for admin endpoints

## Rate Limiting
The API implements rate limiting to prevent abuse. Excessive requests may result in temporary blocks.

## Versioning
Currently, the API does not implement versioning. All endpoints are available at `/api/{endpoint}`.

## Support
For support with the API, please contact the system administrator or refer to the project documentation.