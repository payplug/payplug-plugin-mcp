<?php

namespace PayplugPluginCore\Models\Entities;

class PaymentInputDTO
{
    /** @var string */
    public $api_bearer;

    /** @var string */
    public $payment_method;

    /** @var int */
    public $amount;

    /** @var string */
    public $currency_iso_code;

    /** @var array */
    public $customer; // ['billing' => [...], 'shipping' => [...], 'identifier' => ...]

    /** @var array */
    public $urls; // ['success' => ..., 'cancel' => ..., 'failure' => ...]

    /** @var array */
    public $metadata;

    /** @var array */
    public $context;

    /**
     * @var array[]
     */
    private $definitions = [
        'api_bearer' => ['type' => 'string', 'required' => true],
        'payment_method' => ['type' => 'string', 'required' => true],
        'amount' => ['type' => 'int', 'required' => true],
        'currency_iso_code' => ['type' => 'string', 'required' => true],
        'customer' => ['type' => 'array', 'required' => true],
        'urls' => ['type' => 'array', 'required' => true],
        'metadata' => ['type' => 'array', 'required' => false],
        'context' => ['type' => 'array', 'required' => false],
    ];

    /**
     * @param array $props
     * @return $this|self|null
     */
    public function hydrate(array $props): ?self
    {
        // todo: move this check in validators
        foreach ($this->getDefinitions() as $key => $field) {
            if (!$field['required']) {
                continue;
            }

            if (!array_key_exists($key, $props) || $props[$key] === null) {
                $this->resetProperties();
                throw new \Exception('PaymentInputDTO can\'t be hydrated, required field is invalid.');
            }
        }

        $this->setApiBearer((string)$props['api_bearer']);
        $this->setPaymentMethod((string)$props['payment_method']);
        $this->setAmount((int)$props['amount']);
        $this->setCurrencyIsoCode((string)$props['currency_iso_code']);
        $this->setCustomer((array)$props['customer']);
        $this->setReturnUrls((array)$props['urls']);
        $this->setMetadata((array)$props['metadata']);
        $this->setContext((array)$props['context']);
        return $this;
    }

    /**
     * @return void
     */
    private function resetProperties(): void
    {
        $this->api_bearer = null;
        $this->payment_method = null;
        $this->amount = null;
        $this->currency_iso_code = null;
        $this->customer = null;
        $this->urls = null;
        $this->metadata = null;
        $this->context = null;
    }

    // Getters

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getApiBearer(): string
    {
        return $this->api_bearer;
    }

    /**
     * @return array
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * @return string
     */
    public function getCurrencyIsoCode(): string
    {
        return $this->currency_iso_code;
    }

    /**
     * @return array
     */
    public function getCustomer(): array
    {
        return $this->customer;
    }

    /**
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @return string
     */
    public function getPaymentMethod(): string
    {
        return $this->payment_method;
    }

    /**
     * @return array
     */
    public function getReturnUrls(): array
    {
        return $this->urls;
    }

    /**
     * @return array[]
     */
    public function getDefinitions(): array
    {
        return $this->definitions;
    }

    // Setters

    /**
     * @param int $amount
     * @return void
     */
    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @param string $api_bearer
     * @return void
     */
    public function setApiBearer(string $api_bearer): void
    {
        $this->api_bearer = $api_bearer;
    }

    /**
     * @param array $context
     * @return void
     */
    public function setContext(array $context): void
    {
        $this->context = $context;
    }

    /**
     * @param string $currency_iso_code
     * @return void
     */
    public function setCurrencyIsoCode(string $currency_iso_code): void
    {
        $this->currency_iso_code = $currency_iso_code;
    }

    /**
     * @param array $customer
     * @return void
     */
    public function setCustomer(array $customer): void
    {
        $this->customer = $customer;
    }

    /**
     * @param array $metadata
     * @return void
     */
    public function setMetadata(array $metadata): void
    {
        $this->metadata = $metadata;
    }

    /**
     * @param string $payment_method
     * @return void
     */
    public function setPaymentMethod(string $payment_method): void
    {
        $this->payment_method = $payment_method;
    }

    /**
     * @param array $urls
     * @return void
     */
    public function setReturnUrls(array $urls): void
    {
        $this->urls = $urls;
    }

    /**
     * @param array $props
     * @return self|null
     */
    public static function create(array $props): ?self
    {
        $instance = new self();
        return $instance->hydrate($props);
    }
}