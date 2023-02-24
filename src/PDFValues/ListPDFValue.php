<?php

namespace LSNepomuceno\PhpPdfProcessor\PDFValues;

use LSNepomuceno\PhpPdfProcessor\PDFValues\Traits\ConvertValueTrait;

class ListPDFValue extends BasePDFValue
{
    use ConvertValueTrait;

    public function __construct(mixed $value = [])
    {
        parent::__construct($value);
    }

    public function __toString(): string
    {
        $value = implode(' ', $this->value);

        return "[{$value}]";
    }

    public function diff(mixed $other): bool|array|null|self
    {
        $different = parent::diff($other);

        if (($different === false) || ($different === null)) {
            return $different;
        }

        $selfString  = $this->__toString();
        $otherString = $other->__toString();

        if ($selfString === $otherString) {
            return null;
        }

        return $this;
    }

    public function val(bool $isList = false): mixed
    {
        if ($isList === true) {
            $result = [];
            foreach ($this->value as $v) {
                if (is_a($v, SimplePDFValue::class)) {
                    $v = explode(" ", $v->getValue());
                } else {
                    $v = [$v->getValue()];
                }
                array_push($result, ...$v);
            }
            return $result;
        } else
            return parent::getValue();
    }

    public function getObjectReferenced(): bool|array
    {
        $rebuilt   = '';
        $ids       = [];
        $plainText = trim(implode(' ', $this->value));
        $finds     = (int)preg_match_all('/(([0-9]+)\s+[0-9]+\s+R)[^0-9]*/ms', $plainText, $matches);

        if (empty($plainText) && !$finds) {
            return false;
        }

        if (!empty($plainText) && $finds) {
            $rebuilt   = implode(' ', $matches[0]);
            $rebuilt   = preg_replace('/\s+/ms', ' ', $rebuilt);
            $plainText = preg_replace('/\s+/ms', ' ', $plainText);
        }

        if ($plainText === $rebuilt) {
            foreach ($matches[2] as $id) {
                $ids[] = intval($id);
            }
        }

        return $ids;
    }

    public function push(mixed $value): bool
    {
        if (is_object($value) && (get_class($value) === get_class($this))) {
            // If a list is pushed to another list, the elements are merged
            $value = $value->val();
        }

        if (!is_array($value)) {
            $value = [$value];
        }

        foreach ($value as $element) {
            $element       = self::convert($element);
            $this->value[] = $element;
        }
        return true;
    }

}
