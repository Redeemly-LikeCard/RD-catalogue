# Redeemly Catalogue Integration for Laravel

A Laravel package for seamless integration with Redeemly Catalogue services, designed specifically for customer projects.

## Features

- 🚀 Easy integration with Redeemly Catalogue API
- 🔐 Automatic token management and caching
- 📱 Built for Laravel 10.x and 11.x
- 🎯 Ready for customer projects
- 📝 Full API coverage including customer logs
- 🔄 Automatic retry logic
- 📊 Comprehensive error handling

## Installation

### 1. Install via Composer

```bash
composer require redeemly/catalogue-integration
```

### 2. Publish Configuration

```bash
php artisan vendor:publish --tag="catalogue-config"
```

### 3. Configure Environment Variables

Add the following to your `.env` file:

```env
CATALOGUE_API_BASE_URL=https://your-api-url.com
CATALOGUE_API_KEY=your_api_key
CATALOGUE_CLIENT_ID=your_client_id
CATALOGUE_CACHE_ENABLED=true
CATALOGUE_CACHE_TTL=3600
```

### 4. Register Service Provider (for Laravel < 11)

Add to `config/app.php`:

```php
'providers' => [
    // ...
    Redeemly\CatalogueIntegration\Providers\CatalogueIntegrationServiceProvider::class,
],
```

## Usage

### Using the Facade

```php
use Redeemly\CatalogueIntegration\Facades\Catalogue;

// Get catalogue
$response = Catalogue::getCatalogue();

if ($response->success) {
    $vouchers = $response->data;
    // Process vouchers...
}

// Pull SKU
$skuRequest = new RequestSKUDto(
    voucherId: 'voucher-123',
    quantity: 5,
    orderRef: 'order-456',
    customerRef: 'customer-789'
);

$response = Catalogue::pullSku($skuRequest);
```

### Using Dependency Injection

```php
use Redeemly\CatalogueIntegration\Services\CatalogueService;

class VoucherController extends Controller
{
    public function __construct(private CatalogueService $catalogue) {}
    
    public function index()
    {
        $response = $this->catalogue->getCatalogue();
        return response()->json($response->toArray());
    }
}
```

### Using the Built-in Controller

You can use the built-in controller by adding these routes to your `routes/api.php`:

```php
use Redeemly\CatalogueIntegration\Http\Controllers\CatalogueController;

Route::prefix('catalogue')->group(function () {
    Route::post('external-sign-in', [CatalogueController::class, 'externalSignIn']);
    Route::get('catalogue', [CatalogueController::class, 'getCatalogue']);
    Route::post('pull-sku', [CatalogueController::class, 'pullSku']);
    Route::post('customer-log', [CatalogueController::class, 'getCustomerLog']);
    Route::get('token', [CatalogueController::class, 'getToken']);
});
```

## Available Methods

### CatalogueService

- `externalSignIn(ExternalSignInDto $dto): ApiResponse`
- `getCatalogue(): ApiResponse`
- `pullSku(RequestSKUDto $dto): ApiResponse`
- `getCustomerLog(CustomerLogQueryDto $dto): ApiResponse`
- `ensureValidToken(): string`
- `clearTokenCache(): void`

### Facade

All service methods are available through the `Catalogue` facade.

## Data Transfer Objects (DTOs)

### ExternalSignInDto
```php
new ExternalSignInDto(
    apiKey: 'your-api-key',
    clientId: 'your-client-id'
);
```

### RequestSKUDto
```php
new RequestSKUDto(
    voucherId: 'voucher-123',
    quantity: 5,
    orderRef: 'order-456', // optional
    customerRef: 'customer-789', // optional
    transactionId: 'transaction-123' // optional
);
```

### CustomerLogQueryDto
```php
new CustomerLogQueryDto(
    page: 1,
    pageSize: 10,
    customerRef: 'customer-789', // optional
    customerLogType: 'New' // optional: New, Revealed, Redeemed, Expired
);
```

## API Endpoints

The package provides the following API endpoints when using the built-in controller:

- `POST /api/catalogue/external-sign-in` - External sign in
- `GET /api/catalogue/catalogue` - Get catalogue vouchers
- `POST /api/catalogue/pull-sku` - Pull SKU codes
- `POST /api/catalogue/customer-log` - Get customer logs
- `GET /api/catalogue/token` - Get valid access token

## Configuration

The package configuration file `config/catalogue.php` allows you to customize:

- API base URL
- HTTP client settings (timeout, retry logic)
- Token caching settings
- API credentials

## Error Handling

All API methods return an `ApiResponse` object with:

- `success: bool` - Whether the request was successful
- `error: ErrorDto|null` - Error details if failed
- `data: mixed` - Response data if successful
- `sourceProvider: string|null` - API source information

## Caching

The package automatically caches access tokens to improve performance:

- Cache TTL: 3600 seconds (1 hour) by default
- Configurable via `CATALOGUE_CACHE_TTL` environment variable
- Can be disabled via `CATALOGUE_CACHE_ENABLED=false`

## Logging

All API errors are automatically logged to Laravel's default logger with detailed error information.

## Customer Project Ready

This package is specifically designed for customer projects with:

- ✅ Production-ready error handling
- ✅ Comprehensive logging
- ✅ Configurable timeouts and retry logic
- ✅ Token caching for performance
- ✅ Laravel best practices
- ✅ Full API documentation
- ✅ Easy installation and setup

## Support

For support and questions, please contact Redeemly support team.

## License

This package is licensed under the MIT License.
