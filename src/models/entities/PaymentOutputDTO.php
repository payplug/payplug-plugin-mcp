<?php

namespace PayplugPluginCore\Models\Entities;

class PaymentOutputDTO
{
    /** @var bool */
    public $result;
    /** @var string */
    public $code;
    /** @var string */
    public $message;
    /** @var object */
    public $resource;

    /**
     * @var array[]
     */
    private $definitions = [
        'result' => ['type' => 'boolean', 'required' => true],
        'code' => ['type' => 'string', 'required' => true],
        'message' => ['type' => 'string', 'required' => false],
        'resource' => ['type' => 'object', 'required' => false],
    ];

    /**
     * @param array $props
     *
     * @return $this|self|null
     */
    public function hydrate(array $props): ?self
    {
        // todo: move this check in validators
        foreach ($this->getDefinitions() as $key => $field) {
            if (!$field['required']) {
                continue;
            }

            if (!array_key_exists($key, $props) || null === $props[$key]) {
                $this->resetProperties();

                throw new \Exception('PaymentOutputDTO can\'t be hydrated, required field is invalid.');
            }
        }

        $this->setResult((bool) $props['result']);
        $this->setCode((string) $props['code']);
        $this->setMessage(isset($props['message']) ? (string) $props['message'] : '');
        $this->setResource($props['response']);

        return $this;
    }

    // Getters

    /**
     * @return bool|null
     */
    public function getResult(): ?bool
    {
        return $this->result;
    }

    /**
     * @return string|null
     */
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @return string|null
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @return array[]|null
     */
    public function getDefinitions(): array
    {
        return $this->definitions;
    }

    /**
     * @return object
     */
    public function getResource(): ?object
    {
        return $this->resource;
    }

    // Setters

    /**
     * @param bool $result
     *
     * @return void
     */
    public function setResult(bool $result): void
    {
        $this->result = $result;
    }

    /**
     * @param string $code
     *
     * @return void
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /**
     * @param string $message
     *
     * @return void
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * @param $resource
     *
     * @return void
     */
    public function setResource($resource): void
    {
        $this->resource = $resource;
    }

    /**
     * @param array $props
     *
     * @return self|null
     */
    public static function create(array $props): ?self
    {
        $instance = new self();
        return $instance->hydrate($props);
    }

    /**
     * @return void
     */
    private function resetProperties(): void
    {
        $this->result = null;
        $this->code = null;
        $this->message = null;
        $this->resource = null;
    }
}
