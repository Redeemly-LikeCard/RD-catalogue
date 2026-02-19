<?php

use Illuminate\Support\Facades\Route;
use Redeemly\CatalogueIntegration\Http\Controllers\CatalogueController;

Route::prefix('api/catalogue')->middleware(['api'])->group(function () {
    Route::post('external-sign-in', [CatalogueController::class, 'externalSignIn']);
    Route::get('catalogue', [CatalogueController::class, 'getCatalogue']);
    Route::post('pull-sku', [CatalogueController::class, 'pullSku']);
    Route::get('customer-log', [CatalogueController::class, 'getCustomerLog']);
    Route::get('token', [CatalogueController::class, 'getToken']);
});

/*
|--------------------------------------------------------------------------
| Catalogue Integration Routes
|--------------------------------------------------------------------------
|
| These routes are provided by the Redeemly Catalogue Integration package.
| They provide API endpoints for catalogue operations including:
|
| - External sign-in for authentication
| - Catalogue retrieval
| - SKU pulling
| - Customer log queries
| - Token management
|
| You can customize these routes by modifying the prefix, middleware,
| or controller methods as needed for your application.
|
*/
