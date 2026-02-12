<?php

namespace Redeemly\CatalogueIntegration\Models;

class ApiResponse
{
    public function __construct(
        public readonly bool $success,
        public readonly ?ErrorDto $error = null,
        public readonly mixed $data = null,
        public readonly ?string $sourceProvider = null
    ) {}

    /**
     * Create a successful response
     */
    public static function success(mixed $data = null, ?string $sourceProvider = null): self
    {
        return new self(true, null, $data, $sourceProvider);
    }

    /**
     * Create an error response
     */
    public static function error(string $message, string $code = 'ERROR', ?string $sourceProvider = null): self
    {
        return new self(false, new ErrorDto($message, $code), null, $sourceProvider);
    }

    /**
     * Convert from JSON response
     */
    public static function fromJson(array $data): self
    {
        return new self(
            $data['success'] ?? false,
            isset($data['error']) ? new ErrorDto(
                $data['error']['message'] ?? 'Unknown error',
                $data['error']['code'] ?? 'ERROR'
            ) : null,
            $data['data'] ?? null,
            $data['sourceProvider'] ?? null
        );
    }

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'error' => $this->error?->toArray(),
            'data' => $this->data,
            'sourceProvider' => $this->sourceProvider,
        ];
    }
}
