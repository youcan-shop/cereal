<?php

namespace Tests\Helpers;

class Cloner
{
    /** @var array<object|string> */
    public array $data;

    /**
     * @param array<object|string> $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function __clone()
    {
        $this->data = array_map(
            fn($data) => is_object($data) ? clone $data : $data,
            $this->data
        );
    }
}
