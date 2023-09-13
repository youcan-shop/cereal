<?php

namespace Tests\Components;

use Exception;
use InvalidArgumentException;
use ReflectionProperty;
use ReflectionType;
use YouCan\Cereal\Contracts\SerializationHandler;
use YouCan\Cereal\Contracts\SerializationHandlerFactory as FactoryContract;

class SerializationHandlerFactory implements FactoryContract
{
    /**
     * @param ReflectionProperty $property
     *
     * @return SerializationHandler
     * @throws Exception
     */
    public function getHandler(ReflectionProperty $property): SerializationHandler
    {
        $type = $property->getType();
        if (!$type instanceof ReflectionType) {
            throw new InvalidArgumentException('untyped property');
        }

        if ((string)$type === 'string') {
            return new StringSerializationHandler();
        }

        throw new Exception('non exhaustive handling of types');
    }
}