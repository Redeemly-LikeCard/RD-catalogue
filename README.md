# Redeemly Catalogue Integration for Laravel

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/PHP-8.2+-blue.svg)](https://php.net)
[![Laravel Version](https://img.shields.io/badge/Laravel-10.x%20%7C%2011.x-red.svg)](https://laravel.com)
[![GitHub Repo](https://img.shields.io/badge/GitHub-Redeemly--hub/CatalougePakage-green.svg)](https://github.com/Redeemly-hub/CatalougePakage)

A comprehensive Laravel package for seamless integration with Redeemly Catalogue services. Designed for enterprise customer projects with production-ready features including automatic token management, comprehensive error handling, and full API coverage.

## 🚀 Features

- � **Easy Integration** - Seamless integration with Redeemly Catalogue API
- 🔐 **Automatic Token Management** - Smart token caching and refresh
- 📱 **Laravel Ready** - Built for Laravel 10.x and 11.x with auto-discovery
- 🎯 **Production Ready** - Enterprise-grade error handling and logging
- 📝 **Full API Coverage** - Complete catalogue, SKU pull, and customer logs
- 🔄 **Retry Logic** - Automatic retry with configurable attempts
- 📊 **Comprehensive Logging** - Detailed request/response logging
- 🧪 **Test Dashboard** - Built-in testing interface for development
- ⚡ **Performance Optimized** - Intelligent caching and connection pooling

## 📋 Requirements

- PHP 8.2 or higher
- Laravel 10.x or 11.x
- Composer

## 📦 Installation

### Option 1: Install via Composer (Recommended)

```bash
composer require redeemly/catalogue-integration
```

*Note: This will be available after publishing to Packagist*

### Option 2: Install from GitHub

```bash
composer require redeemly-hub/catalougepakage:dev-master
```

### Step 2: Publish Configuration (Optional)

```bash
php artisan vendor:publish --provider="Redeemly\CatalogueIntegration\Providers\CatalogueIntegrationServiceProvider" --tag="catalogue-config"
```

### Step 3: Configure Environment Variables

Add the following to your `.env` file:

```env
# Catalogue Integration Configuration
CATALOGUE_API_BASE_URL=https://api-stg-luckycode.redeemly.com
CATALOGUE_API_KEY=your_api_key_here
CATALOGUE_CLIENT_ID=your_client_id_here
CATALOGUE_HTTP_TIMEOUT=30
CATALOGUE_CACHE_ENABLED=true
CATALOGUE_CACHE_TTL=3600
CATALOGUE_ROUTES_AUTO_LOAD=true
```
CATALOGUE_CLIENT_ID=your_client_id_here
CATALOGUE_HTTP_TIMEOUT=30
CATALOGUE_CACHE_ENABLED=true
CATALOGUE_CACHE_TTL=3600
CATALOGUE_ROUTES_AUTO_LOAD=true
```

### Automatic Route Loading

By default, the package automatically loads API routes with the prefix `api/catalogue`. The routes include:

- `POST /api/catalogue/external-sign-in` - Authenticate and get token
- `GET /api/catalogue/catalogue` - Retrieve catalogue vouchers
- `POST /api/catalogue/pull-sku` - Pull SKU codes
- `GET /api/catalogue/customer-log` - Get customer transaction logs
- `GET /api/catalogue/token` - Get current access token

**To disable automatic route loading:**

```env
CATALOGUE_ROUTES_AUTO_LOAD=false
```

Then manually add routes as shown below.

### Step 4: Service Provider Registration (Laravel < 11 only)

For Laravel versions below 11, add the service provider to `config/app.php`:

```php
'providers' => [
    // ... other providers
    Redeemly\CatalogueIntegration\Providers\CatalogueIntegrationServiceProvider::class,
],
```

## ⚙️ Configuration

The package provides comprehensive configuration options in `config/catalogue.php`:

### API Configuration
```php
'base_url' => env('CATALOGUE_API_BASE_URL', 'https://api-stg-luckycode.redeemly.com'),
'credentials' => [
    'api_key' => env('CATALOGUE_API_KEY'),
    'client_id' => env('CATALOGUE_CLIENT_ID'),
],
```

### HTTP Client Settings
```php
'http' => [
    'timeout' => env('CATALOGUE_HTTP_TIMEOUT', 30),
    'retry_times' => env('CATALOGUE_HTTP_RETRY_TIMES', 3),
    'retry_delay' => env('CATALOGUE_HTTP_RETRY_DELAY', 1000),
],
```

### Caching Configuration
```php
'cache' => [
    'enabled' => env('CATALOGUE_CACHE_ENABLED', true),
    'ttl' => env('CATALOGUE_CACHE_TTL', 3600),
    'key' => env('CATALOGUE_CACHE_KEY', 'catalogue_access_token'),
],
```

## 🚀 Usage

### Using the Facade (Recommended)

```php
use Redeemly\CatalogueIntegration\Facades\Catalogue;

class VoucherController extends Controller
{
    public function index()
    {
        // Get catalogue vouchers
        $response = Catalogue::getCatalogue();

        if ($response->success) {
            $vouchers = $response->data;
            return view('vouchers.index', compact('vouchers'));
        }

        return back()->withErrors(['error' => $response->error->message]);
    }

    public function pullSku(Request $request)
    {
        $skuRequest = new RequestSKUDto(
            voucherId: $request->voucher_id,
            quantity: $request->quantity,
            orderRef: $request->order_reference,
            customerRef: $request->customer_reference
        );

        $response = Catalogue::pullSku($skuRequest);

        if ($response->success) {
            return response()->json([
                'success' => true,
                'data' => $response->data
            ]);
        }

        return response()->json([
            'success' => false,
            'error' => $response->error->message
        ], 400);
    }
}
```

### Using Dependency Injection

```php
use Redeemly\CatalogueIntegration\Services\CatalogueService;

class CatalogueManager
{
    public function __construct(
        private CatalogueService $catalogueService
    ) {}

    public function getCustomerLogs(string $customerRef, int $page = 1, int $limit = 10)
    {
        $query = new CustomerLogQueryDto(
            page: $page,
            pageSize: $limit,
            customerRef: $customerRef
        );

        return $this->catalogueService->getCustomerLog($query);
    }
}
```

### Optional: Publish Routes

To publish the API routes to your project:

```bash
php artisan vendor:publish --provider="Redeemly\CatalogueIntegration\Providers\CatalogueIntegrationServiceProvider" --tag="catalogue-routes"
```

This will create `routes/catalogue.php` in your project. You can then include it in your main routes file:

```php
// In routes/api.php or routes/web.php
require __DIR__.'/catalogue.php';
```

### Using the Built-in API Routes

Add these routes to your `routes/api.php` for quick API access:

```php
use Redeemly\CatalogueIntegration\Http\Controllers\CatalogueController;

Route::prefix('api/catalogue')->middleware(['auth:sanctum'])->group(function () {
    Route::post('external-sign-in', [CatalogueController::class, 'externalSignIn']);
    Route::get('catalogue', [CatalogueController::class, 'getCatalogue']);
    Route::post('pull-sku', [CatalogueController::class, 'pullSku']);
    Route::get('customer-log', [CatalogueController::class, 'getCustomerLog']);
    Route::get('token', [CatalogueController::class, 'getToken']);
});
```

**Note:** Routes are not loaded automatically by the package. You must manually add them or publish them to maintain full control over your application's routing.

## 📚 API Reference

### CatalogueService Methods

#### `externalSignIn(ExternalSignInDto $dto): ApiResponse`
Authenticate and get access token.

#### `getCatalogue(): ApiResponse`
Retrieve all available catalogue vouchers.

#### `pullSku(RequestSKUDto $dto): ApiResponse`
Pull SKU codes for a specific voucher.

#### `getCustomerLog(CustomerLogQueryDto $dto): ApiResponse`
Get customer transaction logs with pagination.

#### `ensureValidToken(): string`
Get a valid cached token or fetch new one.

#### `clearTokenCache(): void`
Clear the cached access token.

### Data Transfer Objects (DTOs)

#### ExternalSignInDto
```php
$signIn = new ExternalSignInDto(
    apiKey: 'your-api-key',
    clientId: 'your-client-id'
);
```

#### RequestSKUDto
```php
$skuRequest = new RequestSKUDto(
    voucherId: 'voucher-123',      // Required
    quantity: 5,                   // Required
    orderRef: 'order-456',         // Optional
    customerRef: 'customer-789',   // Optional
    transactionId: 'txn-123'       // Optional
);
```

#### CustomerLogQueryDto
```php
$query = new CustomerLogQueryDto(
    page: 1,                       // Optional, default: 1
    pageSize: 10,                  // Optional, default: 10
    customerRef: 'customer-789',   // Optional
    type: 2                        // Optional: 1=New, 2=Revealed, 3=Redeemed, 4=Expired
);
```

### ApiResponse Structure

All methods return an `ApiResponse` object:

```php
class ApiResponse
{
    public bool $success;
    public ?ErrorDto $error;
    public mixed $data;
    public ?string $sourceProvider;

    public function toArray(): array;
}
```

## 🧪 Testing

### Test Dashboard

Access the built-in test dashboard during development:

```
http://your-app.test/test-dashboard
```

### Running Tests

```bash
# Run package tests
composer test

# Run Laravel tests including package
php artisan test
```

### Manual Testing

```php
// Using Tinker
php artisan tinker

>>> use Redeemly\CatalogueIntegration\Facades\Catalogue;
>>> $response = Catalogue::getCatalogue();
>>> $response->success ? $response->data : $response->error->message;
```

## 📊 Error Handling

The package provides comprehensive error handling:

```php
$response = Catalogue::getCatalogue();

if (!$response->success) {
    // Handle error
    Log::error('Catalogue API Error', [
        'message' => $response->error->message,
        'code' => $response->error->code,
        'source' => $response->sourceProvider
    ]);

    // Show user-friendly message
    return back()->withErrors(['catalogue' => 'Unable to load catalogue. Please try again.']);
}
```

## 🔍 Logging

All API requests and errors are automatically logged:

- **Request Logs**: Include endpoint, parameters, and timing
- **Error Logs**: Include full error details and stack traces
- **Token Logs**: Secure token logging (first 20 characters only)

Configure logging in `config/logging.php` as needed.

## 🔧 Advanced Configuration

### Custom HTTP Client

```php
// In service provider
public function register()
{
    $this->app->bind(CatalogueService::class, function ($app) {
        $config = config('catalogue');

        // Custom Guzzle client
        $httpClient = new \GuzzleHttp\Client([
            'base_uri' => $config['base_url'],
            'timeout' => $config['http']['timeout'],
            'proxy' => $config['http']['proxy'] ?? null, // Add proxy support
        ]);

        return new CatalogueService(
            $config['base_url'],
            $config['credentials'],
            $config['http'],
            $app->make(\Illuminate\Contracts\Cache\Repository::class)
        );
    });
}
```

### Custom Cache Store

```php
// Use Redis for token caching
CATALOGUE_CACHE_STORE=redis
CATALOGUE_CACHE_KEY=catalogue:token
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Development Setup

```bash
# Clone repository
git clone https://github.com/redeemly-hub/CatalougePakage.git

# Install dependencies
composer install

# Run tests
composer test

# Run linting
composer lint
```

## 📝 Changelog

### v1.0.0 (Current)
- Initial release with full API integration
- Automatic token management
- Comprehensive error handling
- Laravel 10.x and 11.x support
- Built-in test dashboard

## 📄 License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).

## 🏢 About Redeemly

Redeemly provides enterprise voucher and catalogue management solutions. This package is part of our Laravel integration suite designed for seamless API connectivity.

## 📞 Support

- 📧 **Email**: support@redeemly.com
- 📚 **Documentation**: [Redeemly Developer Portal](https://developers.redeemly.com)
- 🐛 **Issues**: [GitHub Issues](https://github.com/redeemly-hub/CatalougePakage/issues)

---

**Made with ❤️ by Redeemly Team**
