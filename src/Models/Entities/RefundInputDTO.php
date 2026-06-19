<?php

declare(strict_types=1);

namespace PayPlugPluginCore\Models\Entities;

use Exception;
use Payplug\Resource\Payment;

class RefundInputDTO
{
    /** @var string|null */
    public $api_bearer = null;

    /** @var Payment|null */
    public $resource = null;

    /** @var int|null */
    public $amount = null;

    /** @var int|null */
    public $customer_id = null;

    /** @var string|null */
    public $reason = null;

    /**
     * @var array<string, array{type: string, required: bool}>
     */
    private $definitions = [
        'api_bearer'  => ['type' => 'string', 'required' => true],
        'resource'    => ['type' => 'object',  'required' => true],
        'amount'      => ['type' => 'int',    'required' => true],
        'customer_id' => ['type' => 'int',    'required' => false],
        'reason'      => ['type' => 'string', 'required' => false],
    ];

    /**
     * @param array<string, mixed> $props
     * @return $this|self|null
     * @throws Exception
     */
    public function hydrate(array $props): ?self
    {
        // todo: move this check in validators
        foreach ($this->getDefinitions() as $key => $field) {
            if (!$field['required']) {
                continue;
            }

            if (!\array_key_exists($key, $props) || null === $props[$key]) {
                $this->resetProperties();
                throw new Exception('RefundInputDTO can\'t be hydrated, required field is invalid.');
            }
        }

        if (!$props['resource'] instanceof Payment) {
            throw new Exception('RefundInputDTO: resource must be a valid Payment instance.');
        }

        $this->setApiBearer((string) $props['api_bearer']);
        $this->setResource($props['resource']);
        $this->setAmount((int) $props['amount']);
        $this->setCustomerId(isset($props['customer_id']) ? (int) $props['customer_id'] : null);
        $this->setReason(isset($props['reason']) ? (string) $props['reason'] : null);

        return $this;
    }

    private function resetProperties(): void
    {
        $this->api_bearer  = null;
        $this->resource = null;
        $this->amount      = null;
        $this->customer_id = null;
        $this->reason      = null;
    }

    // Getters

    public function getApiBearer(): ?string
    {
        return $this->api_bearer;
    }

    public function getResource(): ?Payment
    {
        return $this->resource;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function getCustomerId(): ?int
    {
        return $this->customer_id;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    /**
     * @return array<string, array{type: string, required: bool}>
     */
    public function getDefinitions(): array
    {
        return $this->definitions;
    }

    // Setters

    public function setApiBearer(string $api_bearer): void
    {
        $this->api_bearer = $api_bearer;
    }

    public function setResource(Payment $resource): void
    {
        $this->resource = $resource;
    }

    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    public function setCustomerId(?int $customer_id): void
    {
        $this->customer_id = $customer_id;
    }

    public function setReason(?string $reason): void
    {
        $this->reason = $reason;
    }

    /**
     * @param array<string, mixed> $props
     * @return self|null
     * @throws Exception
     */
    public static function create(array $props): ?self
    {
        return (new self())->hydrate($props);
    }
}
