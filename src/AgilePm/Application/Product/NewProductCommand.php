<?php declare(strict_types=1);


namespace App\AgilePm\Application\Product;


class NewProductCommand
{
    /**
     * @var string
     */
    private $tenantId;
    /**
     * @var string
     */
    private $productOwnerId;
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $description;

    public function __construct(string $tenantId, string $productOwnerId, string $name, string $description)
    {
        $this->tenantId = $tenantId;
        $this->productOwnerId = $productOwnerId;
        $this->name = $name;
        $this->description = $description;
    }

    public function getTenantId(): string
    {
        return $this->tenantId;
    }

    public function getProductOwnerId(): string
    {
        return $this->productOwnerId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }
}