# QRIS Payment Gateway

A multi-bank QRIS payment gateway system built with Laravel 12 and Filament 3.

## Features

- Master Admin panel for managing QRIS entries
- Member dashboard with balance and transaction history
- RESTful API for payment integration
- Multi-bank QRIS support
- Transaction reporting with export capabilities
- Role-based access control (Master Admin, Admin, Member)
- QRIS distribution strategies (random, round-robin, specific)

## Requirements

- PHP 8.2+
- MySQL 5.7+
- Composer
- Node.js and NPM

## Installation

1. Clone the repository:
   ```
   git clone <repository-url>
   ```

2. Install PHP dependencies:
   ```
   composer install
   ```

3. Install Node dependencies:
   ```
   npm install
   ```

4. Copy and configure the `.env` file:
   ```
   cp .env.example .env
   ```
   Update the database credentials and other configuration values.

5. Generate application key:
   ```
   php artisan key:generate
   ```

6. Run database migrations and seeders:
   ```
   php artisan migrate --seed
   ```

7. Build frontend assets:
   ```
   npm run build
   ```

## Usage

1. Start the development server:
   ```
   php artisan serve
   ```

2. Access the application:
   - Main page: http://localhost:8000
   - Master Admin: http://localhost:8000/admin
   - Member dashboard: http://localhost:8000/member
   - API documentation: http://localhost:8000/docs

3. Login credentials (from seeder):
   - Master Admin: master@example.com / password
   - Admin: admin@example.com / password
   - Member: member@example.com / password

## API Endpoints

### Authentication
- `POST /api/auth/login` - Authenticate user and obtain access token
- `GET /api/auth/user` - Get authenticated user information
- `POST /api/auth/logout` - Revoke access token

### Public Endpoints
- `GET /api/test-api` - Test if the API is working
- `POST /api/payment/callback` - Handle payment notifications

### Authenticated Endpoints
- `POST /api/payment/generate-qris` - Generate a payment QRIS
- `GET /api/payment/transaction/{id}` - Get transaction status
- `GET /api/payment/qris-list` - Get list of active QRIS

### Admin Endpoints
- `GET /api/admin/reports` - Get reports data (admin users only)
- `GET /api/admin/reports/export` - Export reports data (admin users only)

## API Documentation

For detailed API documentation, please refer to:
- [API_DOCUMENTATION.md](API_DOCUMENTATION.md) - Complete API documentation with examples
- [Postman Collection](postman/QRIS_Payment_Gateway_API.postman_collection.json) - Importable Postman collection for testing

To authenticate with the API:
1. Use the `/api/auth/login` endpoint with valid credentials (see Login credentials below)
2. Include the returned token in the `Authorization` header for subsequent requests: `Authorization: Bearer {token}`

## Console Commands

- `php artisan app:simulate-payment` - Create a simulated payment
- `php artisan app:process-pendings` - Process pending transactions

## Testing

Run the test suite:
```
php artisan test
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).