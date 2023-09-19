<?php

namespace YouCanShop\Cereal\Laravel;

use YouCanShop\Cereal\Contracts\SerializationHandler;
use YouCanShop\Cereal\DefaultSerializationHandler;
use YouCanShop\Cereal\SerializationHandlerFactory as BaseSerializationHandlerFactory;

class SerializationHandlerFactory extends BaseSerializationHandlerFactory
{
    public function getHandler(string $type): SerializationHandler
    {
        $handler = parent::getHandler($type);
        if (!$handler instanceof DefaultSerializationHandler) {
            return $handler;
        }

        if (is_subclass_of($type, \Illuminate\Database\Eloquent\Model::class)) {
            return new ModelSerializationHandler();
        }

        return $handler;
    }
}
