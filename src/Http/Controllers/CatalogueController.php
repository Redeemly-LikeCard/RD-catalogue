<?php

namespace Redeemly\CatalogueIntegration\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Redeemly\CatalogueIntegration\Facades\Catalogue;
use Redeemly\CatalogueIntegration\Models\ExternalSignInDto;
use Redeemly\CatalogueIntegration\Models\RequestSKUDto;
use Redeemly\CatalogueIntegration\Models\CustomerLogQueryDto;

class CatalogueController extends Controller
{
    /**
     * External sign in to get access token
     */
    public function externalSignIn(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'apiKey' => 'required|string',
            'clientId' => 'required|string',
        ]);

        try {
            $dto = new ExternalSignInDto($validated['apiKey'], $validated['clientId']);
            $response = Catalogue::externalSignIn($dto);
            
            return response()->json($response->toArray());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'message' => $e->getMessage(),
                    'code' => 'VALIDATION_ERROR'
                ]
            ], 500);
        }
    }

    /**
     * Get catalogue vouchers
     */
    public function getCatalogue(): JsonResponse
    {
        try {
            $response = Catalogue::getCatalogue();
            
            return response()->json($response->toArray());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'message' => $e->getMessage(),
                    'code' => 'INTERNAL_ERROR'
                ]
            ], 500);
        }
    }

    /**
     * Pull SKU codes
     */
    public function pullSku(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'voucherId' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'orderRef' => 'nullable|string',
            'customerRef' => 'nullable|string',
            'transactionId' => 'nullable|string',
        ]);

        try {
            $dto = new RequestSKUDto(
                $validated['voucherId'],
                $validated['quantity'],
                $validated['orderRef'] ?? null,
                $validated['customerRef'] ?? null,
                $validated['transactionId'] ?? null
            );
            
            $response = Catalogue::pullSku($dto);
            
            return response()->json($response->toArray());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'message' => $e->getMessage(),
                    'code' => 'VALIDATION_ERROR'
                ]
            ], 500);
        }
    }

    /**
     * Get customer logs with pagination and filtering
     */
    public function getCustomerLog(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'page' => 'nullable|integer|min:1',
            'pageSize' => 'nullable|integer|min:1|max:100',
            'customerRef' => 'nullable|string',
            'customerLogType' => 'nullable|string|in:New,Revealed,Redeemed,Expired',
        ]);

        try {
            $dto = new CustomerLogQueryDto(
                $validated['page'] ?? 1,
                $validated['pageSize'] ?? 10,
                $validated['customerRef'] ?? null,
                $validated['customerLogType'] ?? null
            );
            
            $response = Catalogue::getCustomerLog($dto);
            
            return response()->json($response->toArray());
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'message' => $e->getMessage(),
                    'code' => 'VALIDATION_ERROR'
                ]
            ], 500);
        }
    }

    /**
     * Get valid access token
     */
    public function getToken(): JsonResponse
    {
        try {
            $token = Catalogue::ensureValidToken();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'accessToken' => $token
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => [
                    'message' => $e->getMessage(),
                    'code' => 'INTERNAL_ERROR'
                ]
            ], 500);
        }
    }
}
