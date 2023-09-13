<?php

namespace YouCan\Cereal;

use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionType;
use YouCan\Cereal\Contracts\Serializable;

final class Serializer
{
    private Serializable $serializable;

    /**
     * this array matches property names to serializations
     * e.g. "store" => STORE_UUID
     *
     * @var array<string, mixed>
     */
    private array $serializations = [];

    public function __construct(Serializable $serializable)
    {
        $this->serializable = $serializable;
    }

    /**
     * @return string[]
     * @throws ReflectionException
     */
    public function __sleep(): array
    {
        $this->serialize();

        return ['serializations', 'serializable'];
    }

    /**
     * @throws ReflectionException
     */
    private function serialize(): void
    {
        foreach ($this->serializable->serializes() as $propertyName) {
            $type = $this->reflect()
                ->getProperty($propertyName)
                ->getType();

            if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                $this->serializations[$propertyName] = $this->serializable->$propertyName;

                continue;
            }

            $this->serializations[$propertyName] = $this->getSerializationHandlerFactory()
                ->getHandler($type->getName())
                ->serialize($this->serializable->$propertyName);
        }
    }

    /**
     * @return ReflectionClass<object>
     */
    private function reflect(): ReflectionClass
    {
        return new ReflectionClass($this->serializable);
    }

    public function getSerializationHandlerFactory(): SerializationHandlerFactory
    {
        return SerializationHandlerFactory::getInstance();
    }

    /**
     * @throws ReflectionException
     */
    public function __wakeup(): void
    {
        $this->deserialize();
    }

    /**
     * @throws ReflectionException
     */
    public function deserialize(): void
    {
        foreach ($this->serializable->serializes() as $propertyName) {
            $type = $this
                ->reflect()
                ->getProperty($propertyName)
                ->getType();

            if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                $this->serializable->$propertyName = $this->serializations[$propertyName];
                continue;
            }

            $serialized = $this->serializations[$propertyName];

            $this->serializable->$propertyName = $this->getSerializationHandlerFactory()
                ->getHandler($type->getName())
                ->deserialize($serialized);
        }
    }
}
