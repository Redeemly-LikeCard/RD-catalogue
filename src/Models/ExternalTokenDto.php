<?php

namespace Redeemly\CatalogueIntegration\Models;

class ExternalTokenDto
{
    public function __construct(
        public readonly string $accessToken,
        public readonly ?int $expiresIn = null
    ) {}

    /**
     * Create from JSON response
     */
    public static function fromJson(array $data): self
    {
        return new self(
            $data['accessToken'] ?? $data['access_token'] ?? '',
            $data['expiresIn'] ?? $data['expires_in'] ?? null
        );
    }
}
