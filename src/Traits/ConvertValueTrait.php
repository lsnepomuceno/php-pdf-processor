<?php

namespace LSNepomuceno\PhpPdfProcessor\PDFValues\Traits;

use LSNepomuceno\PhpPdfProcessor\PDFValues\ListPDFValue;
use LSNepomuceno\PhpPdfProcessor\PDFValues\ObjectPDFValue;
use LSNepomuceno\PhpPdfProcessor\PDFValues\SimplePDFValue;
use LSNepomuceno\PhpPdfProcessor\PDFValues\StringPDFValue;
use LSNepomuceno\PhpPdfProcessor\PDFValues\TypePDFValue;

trait ConvertValueTrait
{
    protected static function convert($value)
    {
        $type = gettype($value);

        if (in_array($type, ['integer', 'double'])) {
            $value = new SimplePDFValue($value);
        }

        if ($type == 'string') {
            $value = match (true) {
                $value[0] === '/'                  => new TypePDFValue(substr($value, 1)),
                preg_match("/\s/ms", $value) === 1 => new StringPDFValue($value),
                default                            => new SimplePDFValue($value)
            };
        }

        if ($type == 'array') {
            if (!count($value)) {
                $value = new ListPDFValue();
            }

            if (count($value)) {
                $obj = ObjectPDFValue::fromArray($value);
                if ($obj !== false) {
                    $value = $obj;
                } else {
                    $list = [];
                    foreach ($value as $v) {
                        $list[] = self::convert($v);
                    }
                    $value = new ListPDFValue($list);
                }
            }
        }

        return $value;
    }
}
