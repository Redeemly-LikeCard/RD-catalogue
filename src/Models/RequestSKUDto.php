<?php

namespace Redeemly\CatalogueIntegration\Models;

class RequestSKUDto
{
    public function __construct(
        public readonly string $voucherId,
        public readonly int $quantity,
        public readonly ?string $orderRef = null,
        public readonly ?string $customerRef = null,
        public readonly ?string $transactionId = null
    ) {}

    /**
     * Convert to array for API request
     */
    public function toArray(): array
    {
        return array_filter([
            'voucherId' => $this->voucherId,
            'quantity' => $this->quantity,
            'orderRef' => $this->orderRef,
            'customerRef' => $this->customerRef,
            'transactionId' => $this->transactionId,
        ]);
    }
}
