<?php

use YouCan\Cereal\Contracts\SerializationHandler;
use YouCan\Cereal\Contracts\SerializationHandlerFactory;
use YouCan\Cereal\Serializer;

it('serializes', function () {
    $factory = new class implements SerializationHandlerFactory {
        public function getHandler(ReflectionProperty $property): SerializationHandler
        {
            $type = $property->getType();
            if (!$type instanceof ReflectionType) {
                throw new InvalidArgumentException('untyped property');
            }

            if ($type->__toString() === 'string') {
                return new class implements SerializationHandler {
                    public function serialize($value)
                    {
                        return $value;
                    }

                    public function deserialize($value)
                    {
                        return $value;
                    }
                };
            }

            throw new Exception('non exhaustive handling of types');
        }
    };

    class A implements \YouCan\Cereal\Contracts\Serializable
    {
        private Serializer $serializer;
        public string $property = 'hello';

        public function __construct($factory)
        {
            $this->serializer = new Serializer($factory, $this);
        }

        public function serializes(): array
        {
            return ['property'];
        }

        public function __sleep()
        {
            return ['serializer'];
        }
    }

    $a = new A($factory);

    serialize($a);

});
