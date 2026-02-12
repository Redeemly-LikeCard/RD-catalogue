<?php

namespace Redeemly\CatalogueIntegration\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Cache\CacheManager;
use Illuminate\Support\Facades\Log;
use Redeemly\CatalogueIntegration\Models\ApiResponse;
use Redeemly\CatalogueIntegration\Models\ExternalSignInDto;
use Redeemly\CatalogueIntegration\Models\ExternalTokenDto;
use Redeemly\CatalogueIntegration\Models\RequestSKUDto;
use Redeemly\CatalogueIntegration\Models\CustomerLogQueryDto;

class CatalogueService
{
    private Client $httpClient;
    private array $credentials;
    private array $httpConfig;
    private CacheManager $cache;
    private ?string $cachedToken = null;

    public function __construct(
        string $baseUrl,
        array $credentials,
        array $httpConfig = [],
        CacheManager $cache
    ) {
        $this->credentials = $credentials;
        $this->httpConfig = $httpConfig;
        $this->cache = $cache;

        $this->httpClient = new Client([
            'base_uri' => $baseUrl,
            'timeout' => $httpConfig['timeout'] ?? 30,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ]);
    }

    /**
     * External sign in to get access token
     */
    public function externalSignIn(ExternalSignInDto $dto): ApiResponse
    {
        try {
            $response = $this->httpClient->post('/account/external-sign-in', [
                'json' => $dto->toArray(),
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            Log::info('Catalogue API External Sign In Response', [
                'http_code' => $response->getStatusCode(),
                'body' => $response->getBody()->getContents(),
                'data' => $data
            ]);
            
            $tokenData = ExternalTokenDto::fromJson($data['data'] ?? []);

            // Cache the token
            $cacheKey = config('catalogue.cache.key', 'catalogue_access_token');
            $ttl = config('catalogue.cache.ttl', 3600);
            if (config('catalogue.cache.enabled', true)) {
                $this->cache->put($cacheKey, $tokenData->accessToken, $ttl);
            }

            return ApiResponse::success($tokenData, 'catalogue-api');
        } catch (RequestException $e) {
            Log::error('Catalogue API External Sign In Error: '.$e->getMessage());
            return ApiResponse::error(
                $e->getMessage(),
                'EXTERNAL_SIGN_IN_ERROR'
            );
        }
    }

    /**
     * Get catalogue vouchers
     */
    public function getCatalogue(): ApiResponse
    {
        try {
            $token = $this->ensureValidToken();
            
            $response = $this->httpClient->get('/catalogue-adapter/view', [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return ApiResponse::fromJson($data);
        } catch (RequestException $e) {
            Log::error('Catalogue API Get Catalogue Error: '.$e->getMessage());
            return ApiResponse::error(
                $e->getMessage(),
                'GET_CATALOGUE_ERROR'
            );
        }
    }

    /**
     * Pull SKU codes
     */
    public function pullSku(RequestSKUDto $dto): ApiResponse
    {
        try {
            $token = $this->ensureValidToken();
            
            $response = $this->httpClient->post('/catalogue-adapter/pull', [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                ],
                'json' => $dto->toArray(),
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return ApiResponse::fromJson($data);
        } catch (RequestException $e) {
            Log::error('Catalogue API Pull SKU Error: '.$e->getMessage());
            return ApiResponse::error(
                $e->getMessage(),
                'PULL_SKU_ERROR'
            );
        }
    }

    /**
     * Get customer logs with pagination and filtering
     */
    public function getCustomerLog(CustomerLogQueryDto $dto): ApiResponse
    {
        try {
            $token = $this->ensureValidToken();
            
            Log::info('Catalogue API Get Customer Log Request', [
                'url' => $this->httpClient->getConfig('base_uri') . '/catalogue-adapter/customer-log',
                'query_params' => $dto->toArray(),
                'token' => substr($token, 0, 20) . '...' // Log first 20 chars for security
            ]);
            
            $response = $this->httpClient->get('/catalogue-adapter/customer-log', [
                'headers' => [
                    'Authorization' => 'Bearer '.$token,
                ],
                'query' => $dto->toArray(),
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            
            Log::info('Catalogue API Get Customer Log Response', [
                'http_code' => $response->getStatusCode(),
                'body' => $response->getBody()->getContents(),
                'data' => $data
            ]);
            
            return ApiResponse::fromJson($data);
        } catch (RequestException $e) {
            Log::error('Catalogue API Get Customer Log Error: '.$e->getMessage());
            return ApiResponse::error(
                $e->getMessage(),
                'GET_CUSTOMER_LOG_ERROR'
            );
        }
    }

    /**
     * Ensure valid access token with caching
     */
    public function ensureValidToken(): string
    {
        $cacheKey = config('catalogue.cache.key', 'catalogue_access_token');
        $ttl = config('catalogue.cache.ttl', 3600);

        // Try to get from cache first
        if (config('catalogue.cache.enabled', true)) {
            $cachedToken = $this->cache->get($cacheKey);
            if ($cachedToken) {
                return $cachedToken;
            }
        }

        // Get new token
        $signInDto = new ExternalSignInDto(
            $this->credentials['api_key'],
            $this->credentials['client_id']
        );

        $response = $this->externalSignIn($signInDto);
        
        if (!$response->success || !$response->data) {
            throw new \Exception('Failed to get access token: '.$response->error?->message);
        }

        /** @var ExternalTokenDto $tokenData */
        $tokenData = $response->data;
        $token = $tokenData->accessToken;

        // Cache the token
        if (config('catalogue.cache.enabled', true)) {
            $this->cache->put($cacheKey, $token, $ttl);
        }

        $this->cachedToken = $token;
        return $token;
    }

    /**
     * Clear cached token
     */
    public function clearTokenCache(): void
    {
        $cacheKey = config('catalogue.cache.key', 'catalogue_access_token');
        $this->cache->forget($cacheKey);
        $this->cachedToken = null;
    }
}
