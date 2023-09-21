<?php

namespace Tests\Helpers;

use YouCanShop\Cereal\Cereal;
use YouCanShop\Cereal\Contracts\Serializable;

class Wrapper implements Serializable
{
    use Cereal;

    public Something $something;

    public function __construct(Something $something)
    {
        $this->something = $something;
    }

    public function getSomething(): Something
    {
        return $this->something;
    }

    public function serializes(): array
    {
        return ['something'];
    }
}
