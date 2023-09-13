<?php

namespace YouCan\Cereal;

trait SerializeTrait
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