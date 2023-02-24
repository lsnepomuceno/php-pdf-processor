<?php

namespace LSNepomuceno\PhpPdfProcessor\PDFValues;

class StringPDFValue extends BasePDFValue
{
    public function __toString(): string
    {
        $value = parent::__toString();
        return "({$value})";
    }
}
