<?php

namespace LSNepomuceno\PhpPdfProcessor\PDFValues;

class SimplePDFValue extends BasePDFValue
{
    public function push(mixed $value): bool
    {
        return match (true) {
            get_class($value) === get_class($this) => function () use ($value) {
                $this->value = $this->value . ' ' . $value->val();
                return true;
            },
            default                                => false
        };
    }

    public function getObjectReferenced(): int|bool
    {
        $isReferenced = preg_match('/^\s*([0-9]+)\s+([0-9]+)\s+R\s*$/ms', $this->value, $matches);

        return match (true) {
            !$isReferenced => false,
            default        => intval($matches[1])
        };
    }

    public function getInt(): int|bool
    {
        return match (true) {
            !is_numeric($this->value) => false,
            default                   => intval($this->value)
        };
    }
}
