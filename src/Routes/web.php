<?php

use Illuminate\Support\Facades\Route;
use Redeemly\CatalogueIntegration\Http\Controllers\CatalogueTestController;

// Catalogue Integration Test Routes (available in all environments for testing)
Route::prefix('catalogue-test')->middleware(['web'])->group(function () {
    Route::get('/', [CatalogueTestController::class, 'dashboard']);
    Route::get('/external-sign-in', [CatalogueTestController::class, 'testExternalSignIn']);
    Route::get('/catalogue', [CatalogueTestController::class, 'testGetCatalogue']);
    Route::post('/pull-sku', [CatalogueTestController::class, 'testPullSku']);
    Route::get('/customer-log', [CatalogueTestController::class, 'testGetCustomerLog']);
    Route::get('/token', [CatalogueTestController::class, 'testGetToken']);
});

// Test dashboard route
Route::get('/test-dashboard', [CatalogueTestController::class, 'dashboard']);
