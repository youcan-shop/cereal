<?php

namespace YouCanShop\Cereal\Contracts;

interface SerializationHandler
{
    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function serialize(Serializable $serializable, $value);

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    public function deserialize(Serializable $serializable, $value);
}
