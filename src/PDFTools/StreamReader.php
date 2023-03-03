<?php

namespace LSNepomuceno\PhpPdfProcessor\PDFTools;

class StreamReader extends ContentBuffer
{
    protected int $position = 0;

    public function __construct(string $content, int $offset = 0)
    {
        parent::__construct($content);

        $this->goTo($offset);
    }

    public function goTo(int $offset): void
    {
        $this->position = min(max(0, $offset), $this->getContentLength());
    }

    public function nextChar(): bool|string
    {
        $this->position = min($this->position + 1, $this->getContentLength());

        return $this->currentChar();

    }

    public function nextChars($n): string
    {
        $n              = min($n, $this->getContentLength() - $this->position);
        $retval         = substr($this->content, $this->position, $n);
        $this->position += $n;

        return $retval;
    }

    private function currentChar(): bool|string
    {
        return match (true) {
            $this->position >= $this->getContentLength()
                    => false,
            default => $this->content[$this->position]
        };
    }

    public function endOfStream(): bool
    {
        return $this->position >= $this->getContent();
    }

    public function subStrAtPos(int $length = 0): string
    {
        return match (true) {
            $length > 0 => substr($this->content, $this->position, $length),
            default     => substr($this->content, $this->position)
        };
    }

    public function getPosition(): int
    {
        return $this->position;
    }
}
