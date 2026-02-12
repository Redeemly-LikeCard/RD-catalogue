<?php

namespace Redeemly\CatalogueIntegration\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Redeemly\CatalogueIntegration\Facades\Catalogue;
use Redeemly\CatalogueIntegration\Models\ExternalSignInDto;
use Redeemly\CatalogueIntegration\Models\RequestSKUDto;
use Redeemly\CatalogueIntegration\Models\CustomerLogQueryDto;

class CatalogueTestController extends Controller
{
    /**
     * Display test dashboard
     */
    public function dashboard(): View
    {
        return view('catalogue-integration::test-dashboard');
    }

    /**
     * Test external sign in
     */
    public function testExternalSignIn(Request $request): JsonResponse
    {
        try {
            $dto = new ExternalSignInDto(
                apiKey: config('catalogue.credentials.api_key'),
                clientId: config('catalogue.credentials.client_id')
            );

            $response = Catalogue::externalSignIn($dto);

            return response()->json([
                'success' => true,
                'message' => 'External sign in successful',
                'data' => $response->data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'External sign in failed',
                'error' => [
                    'message' => $e->getMessage(),
                    'code' => 'EXTERNAL_SIGN_IN_ERROR'
                ]
            ], 500);
        }
    }

    /**
     * Test get catalogue
     */
    public function testGetCatalogue(): JsonResponse
    {
        try {
            $response = Catalogue::getCatalogue();

            return response()->json([
                'success' => true,
                'message' => 'Catalogue retrieved successfully',
                'data' => $response->data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Get catalogue failed',
                'error' => [
                    'message' => $e->getMessage(),
                    'code' => 'GET_CATALOGUE_ERROR'
                ]
            ], 500);
        }
    }

    /**
     * Test pull SKU
     */
    public function testPullSku(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'voucherId' => 'required|string',
                'quantity' => 'required|integer|min:1',
                'orderRef' => 'nullable|string',
                'customerRef' => 'nullable|string',
                'transactionId' => 'nullable|string'
            ]);

            $dto = new RequestSKUDto(
                voucherId: $request->voucherId,
                quantity: $request->quantity,
                orderRef: $request->orderRef,
                customerRef: $request->customerRef,
                transactionId: $request->transactionId
            );

            $response = Catalogue::pullSku($dto);

            return response()->json([
                'success' => true,
                'message' => 'Pull SKU successful',
                'data' => $response->data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pull SKU failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test get customer log
     */
    public function testGetCustomerLog(Request $request): JsonResponse
    {
        try {
            $dto = new CustomerLogQueryDto(
                page: $request->query('Page', 1),
                pageSize: $request->query('PageSize', 10),
                customerRef: $request->query('CustomerRef', 'Net1234'),
                customerLogType: $request->query('type', 2)
            );

            $response = Catalogue::getCustomerLog($dto);

            return response()->json([
                'success' => true,
                'message' => 'Customer log retrieved successfully',
                'data' => $response->data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Get customer log failed',
                'error' => [
                    'message' => $e->getMessage(),
                    'code' => 'GET_CUSTOMER_LOG_ERROR'
                ]
            ], 500);
        }
    }

    /**
     * Test get token
     */
    public function testGetToken(): JsonResponse
    {
        try {
            $token = Catalogue::ensureValidToken();

            return response()->json([
                'success' => true,
                'message' => 'Get token successful',
                'data' => [
                    'token' => $token,
                    'timestamp' => now()->toISOString()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Get token failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
