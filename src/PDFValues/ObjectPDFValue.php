<?php

namespace LSNepomuceno\PhpPdfProcessor\PDFValues;

use LSNepomuceno\PhpPdfProcessor\PDFValues\Traits\ConvertValueTrait;

class ObjectPDFValue extends BasePDFValue
{
    use ConvertValueTrait;

    public function __construct(mixed $value = [])
    {
        $result = [];
        foreach ($value as $key => $val) {
            $result[$key] = self::convert($val);
        }
        parent::__construct($result);
    }

    public function diff(mixed $other): bool|array|null|self
    {
        $isDifferent = parent::diff($other);

        if (($isDifferent === false) || ($isDifferent === null)) {
            return $isDifferent;
        }

        $result      = new ObjectPDFValue;
        $differences = 0;

        foreach ($this->value as $key => $value) {
            if (isset($other->value[$key]) &&
                is_a($this->value[$key], BasePDFValue::class)) {
                $isDifferent = $this->value[$key]->diff($other->value[$key]);

                if ($isDifferent === false) {
                    $result[$key] = $value;
                    $differences++;
                }

                if ($isDifferent !== null) {
                    $result[$key] = $isDifferent;
                    $differences++;
                }
            }

            if (!isset($other->value[$key])) {
                $result[$key] = $value;
                $differences++;
            }
        }

        if ($differences === 0) {
            return null;
        }

        return $result;
    }

    public static function fromArray(array $parts): BasePDFValue|bool
    {
        $keys    = array_keys($parts);
        $intKeys = false;
        $result  = [];

        foreach ($keys as $childKey) {
            if (!is_int($childKey)) {
                continue;
            }
            $intKeys = true;
        }

        if ($intKeys) {
            return false;
        }

        foreach ($parts as $keys => $v) {
            $result[$keys] = self::convert($v);
        }
        return new BasePDFValue($result);
    }

    public static function fromString($str): BasePDFValue|bool
    {
        $result = [];
        $field  = null;
        $parts  = explode(' ', $str);

        for ($i = 0; $i < count($parts); $i++) {
            if ($field === null) {
                $field = $parts[$i];

                if ($field === '' ||
                    $field[0] !== '/' ||
                    substr($field, 1) === ''
                ) {
                    return false;
                }
                continue;
            }
            $value          = $parts[$i];
            $result[$field] = $value;
            $field          = null;
        }
        // If there is no pair of values, there is no valid
        if ($field !== null) {
            return false;
        }

        return new BasePDFValue($result);
    }

    public function getKeys(): array
    {
        return array_keys($this->value);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($value === null && isset($this->value[$offset])) {
            unset($this->value[$offset]);
        }

        $this->value[$offset] = self::convert($value);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->value[$offset]);
    }

    public function __toString(): string
    {
        $result = [];
        foreach ($this->value as $key => $value) {
            $value = '' . $value;
            if ($value === '') {
                $result[] = "/{$key}";
                continue;
            }

            $result[] = match ($value[0]) {
                '/', '[', '(', '<' => "/{$key}{$value}",
                default            => "/{$key} {$value}",
            };
        }
        $result = implode('', $result);
        
        return "<<{$result}>>";
    }


}
