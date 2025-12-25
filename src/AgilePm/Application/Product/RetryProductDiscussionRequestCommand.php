<?php declare(strict_types=1);

namespace App\AgilePm\Application\Product;

class RetryProductDiscussionRequestCommand
{
    private string $tenantId;
    private string $processId;

    public function __construct(string $tenantId = '', string $processId = '')
    {
        $this->tenantId = $tenantId;
        $this->processId = $processId;
    }

    public function getTenantId(): string
    {
        return $this->tenantId;
    }

    public function setTenantId(string $tenantId): void
    {
        $this->tenantId = $tenantId;
    }

    public function getProcessId(): string
    {
        return $this->processId;
    }

    public function setProcessId(string $processId): void
    {
        $this->processId = $processId;
    }
}