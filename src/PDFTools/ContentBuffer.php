<?php

namespace LSNepomuceno\PhpPdfProcessor\PDFTools;

use Stringable;

class ContentBuffer implements Stringable
{
    public function __construct(
        protected string $content,
        protected int    $contentLength = 0
    )
    {
        $this->setContentLength();
    }

    public function addContent(string ...$contents)
    {
        foreach ($contents as $content) {
            $this->content .= $content;
        }
        $this->setContentLength();
    }


    public function __toString(): string
    {
        // TODO: VALIDAR DUMP DE RETORNO
        return '';
    }

    protected function setContentLength(): void
    {
        $this->contentLength = strlen($this->content);
    }

    public function getContentLength(): int
    {
        $this->setContentLength();
        
        return $this->contentLength;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function mergeContentBuffers(ContentBuffer $secondContent): void
    {
        $this->content .= $secondContent->getContent();
    }

    public function mergeMultipleContentBuffers(ContentBuffer ...$otherContentBuffers): string
    {
        $newBuffer = new ContentBuffer('');

        foreach ($otherContentBuffers as $otherContentBuffer) {
            $newBuffer->mergeContentBuffers($otherContentBuffer);
        }

        return $newBuffer->getContent();
    }

    public function clone(): self
    {
        return new ContentBuffer($this->getContent());
    }

    public function getBytes(int $columns, int $offset = 0, int $length = 0): string
    {
        $length = !$length ? $this->getContentLength() : $length;
        $result = '';
        $length = min($length, $this->getContentLength());

        for ($i = $offset; $i < $length;) {
            for ($j = 0; ($j < $columns) && ($i < $length); $i++, $j++) {
                $result .= sprintf('%02x ', ord($this->content[$i]));
            }
            $result .= "\n";
        }

        return $result;
    }
}
