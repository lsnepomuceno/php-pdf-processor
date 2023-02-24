<?php

namespace LSNepomuceno\PhpPdfProcessor\PDFValues;

class ReferencePDFValue extends SimplePDFValue
{
    public function __construct(mixed $value)
    {
        parent::__construct(sprintf("%d 0 R", $value));
    }
}
