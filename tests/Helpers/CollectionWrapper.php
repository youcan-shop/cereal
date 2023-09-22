<?php

namespace Tests\Helpers;

use Illuminate\Support\Collection;
use YouCanShop\Cereal\Cereal;
use YouCanShop\Cereal\Contracts\Serializable;

class CollectionWrapper implements Serializable
{
    use Cereal;

    /** @var Collection<array-key, Something> */
    public Collection $things;

    /**
     * @param Collection<array-key, Something> $things
     */
    public function __construct(Collection $things)
    {
        $this->things = $things;
    }

    public function serializes(): array
    {
        return ['things'];
    }
}
