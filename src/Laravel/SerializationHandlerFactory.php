<?php

namespace YouCanShop\Cereal\Laravel;

use YouCanShop\Cereal\Contracts\SerializationHandler;
use YouCanShop\Cereal\SerializationHandlerFactory as BaseSerializationHandlerFactory;

class SerializationHandlerFactory extends BaseSerializationHandlerFactory
{
    public function getHandler(string $type): SerializationHandler
    {
        if (is_subclass_of($type,'Illuminate\Database\Eloquent\Model')) {
            return new ModelSerializationHandler();
        }

        return parent::getHandler($type);
    }
}
