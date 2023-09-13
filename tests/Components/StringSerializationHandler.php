<?php

namespace Tests\Components;

use YouCan\Cereal\Contracts\SerializationHandler as HandlerContract;

class StringSerializationHandler implements HandlerContract
{
    public function serialize($value)
    {
        return $value;
    }

    public function deserialize($value)
    {
        return $value;
    }
}
