<?php

namespace YouCanShop\Cereal;

trait Cerealizes
{
    private Serializer $serializer;

    public function __serialize()
    {
        return [$this->getSerializerPropertyName() => new Serializer($this)];
    }

    public function getSerializerPropertyName(): string
    {
        return 'serializer';
    }
}