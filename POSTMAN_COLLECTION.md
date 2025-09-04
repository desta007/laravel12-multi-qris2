# QRIS Payment Gateway - Postman Collection

This document describes how to use the Postman collection for the QRIS Payment Gateway API.

## Collection Overview

The Postman collection includes all API endpoints for the QRIS Payment Gateway system, organized into the following folders:

1. **Authentication Endpoints** - APIs for user authentication and token management
2. **Public Endpoints** - APIs that don't require authentication
3. **Authenticated Endpoints** - APIs that require a valid auth token
4. **Admin Endpoints** - APIs restricted to admin users

## Setup Instructions

### 1. Import the Collection

1. Open Postman
2. Click "Import" in the top left
3. Select the `QRIS_Payment_Gateway_API.postman_collection.json` file
4. Click "Import"

### 2. Configure Environment Variables

The collection uses two environment variables:

- `base_url`: The base URL of your API (default: http://localhost:8000)
- `auth_token`: Your authentication token

To configure these variables:

1. Click the "Environment" dropdown in the top right
2. Select "Manage Environments"
3. Click "Add" to create a new environment
4. Set the following variables:
   - `base_url`: Your API base URL (e.g., https://your-domain.com)
   - `auth_token`: Leave blank for now

### 3. Authentication

Most endpoints require authentication using Laravel Sanctum tokens.

To obtain an auth token:

1. Use the "Login" endpoint to authenticate with your email and password
2. Copy the token from the response
3. Set the `auth_token` environment variable to this value

All authenticated requests automatically use this token through the collection's authorization settings.

## Endpoints

### Authentication Endpoints

#### Login
- **Method**: POST
- **URL**: `/api/auth/login`
- **Description**: Authenticate user and obtain access token
- **Authentication**: None required
- **Body Parameters**:
  - `email` (string, required): User's email address
  - `password` (string, required): User's password
- **Response**:
  ```json
  {
    "success": true,
    "data": {
      "user": { /* user data */ },
      "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
      "token_type": "Bearer"
    }
  }
  ```

#### Get Authenticated User
- **Method**: GET
- **URL**: `/api/auth/user`
- **Description**: Get the authenticated user's data
- **Authentication**: Bearer token required

#### Logout
- **Method**: POST
- **URL**: `/api/auth/logout`
- **Description**: Revoke the current access token
- **Authentication**: Bearer token required

### Public Endpoints

#### Test API
- **Method**: GET
- **URL**: `/api/test-api`
- **Description**: Test if the API is working
- **Authentication**: None required

#### Payment Callback
- **Method**: POST
- **URL**: `/api/payment/callback`
- **Description**: Handle payment notifications from QRIS providers
- **Authentication**: None required
- **Body Parameters**:
  - `transaction_id` (string): The transaction identifier
  - `status` (string): Payment status
  - `paid_at` (string, optional): Payment timestamp

### Authenticated Endpoints

#### Generate Payment QRIS
- **Method**: POST
- **URL**: `/api/payment/generate-qris`
- **Description**: Generate a new payment QRIS for a transaction
- **Authentication**: Bearer token required
- **Body Parameters**:
  - `amount` (number): Transaction amount in IDR
  - `description` (string, optional): Transaction description
  - `callback_url` (string, optional): Merchant callback URL
  - `distribution_strategy` (string, optional): QRIS selection strategy (random, round_robin, specific)
  - `qris_id` (number, optional): Specific QRIS ID (required when strategy is "specific")

#### Get Transaction Status
- **Method**: GET
- **URL**: `/api/payment/transaction/:transactionId`
- **Description**: Get the status of a specific transaction
- **Authentication**: Bearer token required
- **Path Parameters**:
  - `transactionId`: The transaction identifier

#### Get QRIS List
- **Method**: GET
- **URL**: `/api/payment/qris-list`
- **Description**: Get list of active QRIS
- **Authentication**: Bearer token required

### Admin Endpoints

#### Get Reports
- **Method**: GET
- **URL**: `/api/admin/reports`
- **Description**: Get reports data
- **Authentication**: Bearer token with admin privileges required

#### Export Reports
- **Method**: GET
- **URL**: `/api/admin/reports/export`
- **Description**: Export reports data
- **Authentication**: Bearer token with admin privileges required

## Example Usage

### 1. Authentication Flow

1. Open the "Login" request in the Authentication folder
2. Update the request body with your email and password:
   ```json
   {
     "email": "your-email@example.com",
     "password": "your-password"
   }
   ```
3. Send the request
4. Copy the token from the response:
   ```json
   {
     "success": true,
     "data": {
       "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
     }
   }
   ```
5. Set the `auth_token` environment variable to the token value (without "Bearer ")

### 2. Making Authenticated Requests

Once you've set the `auth_token` environment variable:

1. Open any authenticated endpoint (e.g., "Generate Payment QRIS")
2. The request will automatically include the Authorization header with your token
3. Send the request

### 3. Generating a Payment QRIS

1. Ensure your `auth_token` environment variable is set
2. Open the "Generate Payment QRIS" request
3. Update the request body with your desired amount and description
4. Send the request
5. The response will include the QRIS code and transaction details

### 4. Checking Transaction Status

1. After generating a QRIS, note the transaction ID from the response
2. Open the "Get Transaction Status" request
3. Replace the `transactionId` path variable with your actual transaction ID
4. Send the request
5. The response will show the current status of the transaction

### 5. Simulating a Payment Callback

1. Open the "Payment Callback" request
2. Update the `transaction_id` in the request body to match a real transaction
3. Send the request
4. Check the transaction status again to verify it was updated to "success"

## Troubleshooting

### Authentication Issues

If you receive 401 Unauthorized errors:
1. Verify your `auth_token` is set correctly
2. Ensure the token hasn't expired
3. Confirm the user has the necessary permissions
4. Make sure the token is prefixed with "Bearer " in the Authorization header

### 404 Not Found

If you receive 404 errors:
1. Verify the `base_url` environment variable is correct
2. Ensure the API is running and accessible
3. Check that the transaction ID exists in the database

### 500 Internal Server Error

If you receive 500 errors:
1. Check the Laravel logs for detailed error information
2. Ensure all required environment variables are set
3. Verify database connectivity

## Security Notes

1. Never share your auth tokens
2. Use HTTPS in production environments
3. Store tokens securely
4. Regularly rotate tokens for enhanced security
5. Always logout to revoke tokens when finished

## Recent Fixes

We've recently fixed an issue where unauthenticated API requests were causing a "Route [login] not defined" error. The solution involved:

1. Creating a custom exception handler that returns JSON responses for API authentication failures
2. Implementing a custom authentication middleware that properly handles API requests
3. Updating the API routes to use the new middleware

We've also added proper authentication endpoints to make it easier to obtain and use API tokens.

These changes ensure that API endpoints now return proper JSON error responses (401 Unauthorized) instead of trying to redirect to a login page.