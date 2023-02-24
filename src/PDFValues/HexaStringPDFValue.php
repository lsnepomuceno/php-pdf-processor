<?php

namespace LSNepomuceno\PhpPdfProcessor\PDFValues;

class HexaStringPDFValue extends StringPDFValue
{
    public function __toString(): string
    {
        $value = trim($this->value);
        return "<{$value}>";
    }
}
