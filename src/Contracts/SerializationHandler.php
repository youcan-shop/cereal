<?php

namespace YouCanShop\Cereal\Contracts;

interface SerializationHandler
{
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function serialize($value);

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function deserialize($value);
}
