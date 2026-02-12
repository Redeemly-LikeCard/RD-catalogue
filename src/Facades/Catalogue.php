<?php

namespace Redeemly\CatalogueIntegration\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Redeemly\CatalogueIntegration\Dto\ApiResponse externalSignIn(\Redeemly\CatalogueIntegration\Dto\ExternalSignInDto $dto)
 * @method static \Redeemly\CatalogueIntegration\Dto\ApiResponse getCatalogue()
 * @method static \Redeemly\CatalogueIntegration\Dto\ApiResponse pullSku(\Redeemly\CatalogueIntegration\Dto\RequestSKUDto $dto)
 * @method static \Redeemly\CatalogueIntegration\Dto\ApiResponse getCustomerLog(\Redeemly\CatalogueIntegration\Dto\CustomerLogQueryDto $dto)
 * @method static string ensureValidToken()
 *
 * @see \Redeemly\CatalogueIntegration\Services\CatalogueService
 */
class Catalogue extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'catalogue';
    }
}
