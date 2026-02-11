<?php

declare(strict_types=1);

namespace PayplugPluginCore\Models\Entities;

class PaymentOutputDTO
{
    public ?bool $result = null;

    public ?string $code = null;

    public ?string $message = null;

    public ?object $resource = null;

    /**
     * @var array<string, array{type: string, required: bool}>
     */
    private array $definitions = [
        'result'   => ['type' => 'boolean', 'required' => true],
        'code'     => ['type' => 'string',  'required' => true],
        'message'  => ['type' => 'string',  'required' => false],
        'resource' => ['type' => 'object',  'required' => false],
    ];

    /**
     * @param array<string, mixed> $props
     * @return $this|self|null
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
                throw new \Exception('PaymentOutputDTO can\'t be hydrated, required field is invalid.');
            }
        }

        $this->setResult((bool) $props['result']);
        $this->setCode((string) $props['code']);
        $this->setMessage(isset($props['message']) ? (string) $props['message'] : '');
        $this->setResource($props['response']);

        return $this;
    }

    private function resetProperties(): void
    {
        $this->result   = null;
        $this->code     = null;
        $this->message  = null;
        $this->resource = null;
    }

    // Getters

    public function getResult(): ?bool
    {
        return $this->result;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * @return array<string, array{type: string, required: bool}>
     */
    public function getDefinitions(): array
    {
        return $this->definitions;
    }

    public function getResource(): ?object
    {
        return $this->resource;
    }

    // Setters

    public function setResult(bool $result): void
    {
        $this->result = $result;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function setResource(mixed $resource): void
    {
        $this->resource = \is_object($resource) ? $resource : null;
    }

    /**
     * @param array<string, mixed> $props
     * @return self|null
     */
    public static function create(array $props): ?self
    {
        return new self()->hydrate($props);
    }
}
