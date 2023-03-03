<?php

namespace LSNepomuceno\PhpPdfProcessor\PDFValues;

use ArrayAccess;
use Exception;
use ReturnTypeWillChange;
use Stringable;

class BasePDFValue implements ArrayAccess, Stringable
{
    protected const BLACKLIST = [
        '*'     => ['Parent'], // Field "Parent" for any type of object
        'Annot' => ['P'] // Field "P" for nodes of type "Annot"
    ];

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

    public function getKeys(): array
    {
        return array_keys($this->value);
    }

    public static function referencesInObject(BasePDFValue $object, mixed $oid = false): array
    {
        $type       = $object['Type'];
        $type       = $type ? $type->val() : '';
        $references = [];

        foreach ($object->getKeys() as $key) {

            if (in_array($key, self::BLACKLIST['*']) ||
                (array_key_exists($type, self::BLACKLIST) && in_array($key, self::BLACKLIST[$type]))
            ) {
                continue;
            }

            if (is_a($object[$key], BasePDFValue::class)) {
                $refObjects = self::referencesInObject($object[$key]);
            } else {
                $refObjects = $object[$key]->getObjectReferenced();

                if ($refObjects === false) {
                    continue;
                }

                if (!is_array($refObjects)) {
                    $refObjects = [$refObjects];
                }
            }

            array_push($references, ...$refObjects);
        }

        return $references;
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
    */
}
