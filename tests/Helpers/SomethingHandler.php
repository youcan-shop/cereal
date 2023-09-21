<?php

namespace Tests\Helpers;

use YouCanShop\Cereal\Contracts\Serializable;
use YouCanShop\Cereal\Contracts\SerializationHandler;

class SomethingHandler implements SerializationHandler
{
    /** @var array{string:Something} */
    private array $table;

    /**
     * @param array{string:Something} $table
     */
    public function __construct(array $table)
    {
        $this->table = $table;
    }

    /**
     * @param Serializable $serializable
     * @param Something $value
     *
     * @return string
     */
    public function serialize(Serializable $serializable, $value): string
    {
        return $value->getId();
    }

    /**
     * @param Serializable $serializable
     * @param string $value
     *
     * @return Something
     */
    public function deserialize(Serializable $serializable, $value): Something
    {
        return $this->table[$value];
    }
}
