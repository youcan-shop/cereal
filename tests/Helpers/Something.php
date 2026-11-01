<?php

namespace Tests\Helpers;

class Something
{
    private string $id;
    private bool $processed = false;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function isProcessed(): bool
    {
        return $this->processed;
    }

    public function setProcessed(bool $processed): Something
    {
        $this->processed = $processed;

        return $this;
    }
}
