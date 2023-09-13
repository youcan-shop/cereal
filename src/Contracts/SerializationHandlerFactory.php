<?php

namespace YouCan\Cereal\Contracts;

use ReflectionProperty;

interface SerializationHandlerFactory
{
    
    public function getHandler(ReflectionProperty $property): SerializationHandler;
}
