<?php

namespace Redeemly\CatalogueIntegration\Models;

class ExternalSignInDto
{
    public function __construct(
        public readonly string $apiKey,
        public readonly string $clientId
    ) {}

    /**
     * Convert to array for API request
     */
    public function toArray(): array
    {
        return [
            'apiKey' => $this->apiKey,
            'clientId' => $this->clientId,
        ];
    }
}
