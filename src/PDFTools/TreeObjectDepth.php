<?php

namespace LSNepomuceno\PhpPdfProcessor\PDFTools;

use Generator;
use Stringable;

class TreeObjectDepth implements Stringable
{
    public function __construct(
        public int     $oid,
        public ?string $info = null,
        public int     $isChild = 0,
        public array   $children = []
    )
    {
    }

    public function addChild(int $oid, TreeObjectDepth $object): void
    {
        $this->children[$oid] = $object;

        $object->isChild += 1;
    }

    public function children(): Generator
    {
        foreach ($this->children as $oid => $object) {
            yield $oid;
        }
    }

    protected function getString(?string $spaces = '', int $childCount = 0): string
    {
        $info  = $this->oid . ($this->info ? " ({$this->info})" : '');
        $lines = [];

        if (is_null($spaces)) {
            $lines = ["{$spaces}  " . json_decode('"\u2501"') . " {$info}"];
        }

        if (!is_null($spaces) && $childCount === 0) {
            $lines = ["{$spaces}  " . json_decode('"\u2514\u2500"') . " {$info}"];
        }

        if (!is_null($spaces) && $childCount > 0) {
            $lines = ["{$spaces}  " . json_decode('"\u251c\u2500"') . " {$info}"];
        }

        $chcount = count($this->children);

        foreach ($this->children as $oid => $child) {
            $chcount--;
            if (($spaces === null) || ($childCount === 0)) {
                $lines[] = $child->_getstr($spaces . '   ', $chcount);
            } else
                $lines[] = $child->_getstr($spaces . '  ' . json_decode('"\u2502"'), $chcount);
        }

        return implode("\n", $lines);
    }

    protected function getOldString(int $depth = 0): string
    {
        $spaces  = str_repeat('   ' . json_decode('"\u2502"'), $depth);
        $start   = "{$spaces}   " . json_decode('"\u251c\u2500"');
        $oid     = $this->oid . ($this->info ? " ({$this->info})" : '');
        $isChild = (($this->isChild > 1) ? " {$this->isChild}" : '');
        $lines   = ["{$start} {$oid}{$isChild}"];

        /**
         * @var TreeObjectDepth $child
         */
        foreach ($this->children as $oid => $child) {
            $lines[] = $child->getString($depth + 1);
        }

        return implode("\n", $lines);
    }

    public function __toString(): string
    {
        return $this->getString(null, count($this->children));
    }
}
