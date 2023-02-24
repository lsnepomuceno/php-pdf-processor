<?php

namespace LSNepomuceno\PhpPdfProcessor\PDFValues;

use ArrayAccess;
use Exception;
use ReturnTypeWillChange;
use Stringable;

class BasePDFValue implements ArrayAccess, Stringable
{
    public function __construct(protected mixed $value)
    {
    }

    public function offsetExists($offset): bool
    {
        return is_array($this->value) && isset($this->value[$offset]);
    }

    #[ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return match (true) {
            !is_array($this->value),
            !isset($this->value[$offset]) => false,
            default                       => $this->value[$offset]
        };
    }

    public function offsetSet($offset, $value): void
    {
        if (is_array($this->value)) {
            $this->value[$offset] = $value;
        }
    }

    /**
     * @throws Exception
     */
    public function offsetUnset($offset): void
    {
        // TODO: Adicionar classe de exceção
        if ((!is_array($this->value)) || (!isset($this->value[$offset]))) {
            throw new Exception('invalid offset');
        }
        unset($this->value[$offset]);
    }

    public function diff(mixed $other): bool|array|null|self
    {
        return match (true) {
            !is_a($other, get_class($this)) => false,
            $this->value === $other->value  => null,
            default                         => $this->value
        };
    }

    public function __toString(): string
    {
        return "{$this->value}";
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }

    /*
    public function push(): bool
    {
        // TODO: Incluir lógica
        return false;
    }

    public function getInt(): bool
    {
        // TODO: Incluir lógica
        return false;
    }

    public function getObjectReferenced(): bool
    {
        // TODO: Incluir lógica
        return false;
    }

    public function getKeys(): bool
    {
        // TODO: Incluir lógica
        return false;
    }
    */
}
