<?php

namespace YouCanShop\Cereal;

use YouCanShop\Cereal\Contracts\Serializable;

/**
 * @mixin Serializable
 */
trait Cereal
{
    protected Serializer $serializer;

    public function __serialize(): array
    {
        /** @var Serializable $this */

        return [
            $this->getSerializerPropertyName() => new Serializer($this),
        ];
    }

    public function getSerializerPropertyName(): string
    {
        return 'serializer';
    }
}
