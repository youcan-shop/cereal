<?php

namespace YouCanShop\Cereal\Laravel;

use YouCanShop\Cereal\Contracts\Serializable;
use YouCanShop\Cereal\Serializer;

trait Cereal
{
    use \YouCanShop\Cereal\Cereal;

    public function __serialize(): array
    {
        /** @var Serializable $this */
        return [
            $this->getSerializerPropertyName() => new Serializer(
                $this,
                SerializationHandlerFactory::class
            ),
        ];
    }
}