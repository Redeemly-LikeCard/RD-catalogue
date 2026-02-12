<?php

namespace Redeemly\CatalogueIntegration\Models;

class ErrorDto
{
    public function __construct(
        public readonly string $message,
        public readonly string $code
    ) {}

    /**
     * Convert to array
     */
    public function toArray(): array
    {
        return [
            'message' => $this->message,
            'code' => $this->code,
        ];
    }
}
