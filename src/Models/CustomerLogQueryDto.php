<?php

namespace Redeemly\CatalogueIntegration\Models;

class CustomerLogQueryDto
{
    public function __construct(
        public readonly int $page = 1,
        public readonly int $pageSize = 10,
        public readonly ?string $customerRef = null,
        public readonly ?string $customerLogType = null
    ) {}

    /**
     * Convert to array for API request
     */
    public function toArray(): array
    {
        return array_filter([
            'page' => $this->page,
            'pageSize' => $this->pageSize,
            'customerRef' => $this->customerRef,
            'customerLogType' => $this->customerLogType,
        ]);
    }
}
