<?php

namespace LSNepomuceno\PhpPdfProcessor\PDFValues;

class TypePDFValue extends BasePDFValue
{
    public function __toString(): string
    {
        $value = trim(parent::__toString());
        return "/{$value}";
    }
}
