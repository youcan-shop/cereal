<?php

namespace YouCanShop\Cereal\Contracts;

interface Serializable
{
    /**
     * returns an array of property names to serialize
     * the property must satisfy the following criteria:
     *   it must be explicitly typed,
     *   it must be publicly accessible.
     *
     * @return array<string>
     */
    public function serializes(): array;
}
