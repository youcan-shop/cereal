<?php

namespace YouCan\Cereal;

use ReflectionClass;
use ReflectionException;
use YouCan\Cereal\Contracts\Serializable;
use YouCan\Cereal\Contracts\SerializationHandlerFactory;

final class Serializer
{
    private SerializationHandlerFactory $handlerFactory;
    private Serializable $serializable;

    /**
     * this array matches property names to serializations
     * e.g. "store" => STORE_UUID
     *
     * @var array<string, mixed>
     */
    private array $serializations = [];

    public function __construct(
        SerializationHandlerFactory $handlerFactory,
        Serializable $serializable
    ) {
        $this->handlerFactory = $handlerFactory;
        $this->serializable = $serializable;
    }

    /**
     * @return string[]
     * @throws ReflectionException
     */
    public function __sleep(): array
    {
        $this->serialize();

        return ['handlerFactory', 'serializations', 'serializable'];
    }

    /**
     * @throws ReflectionException
     */
    private function serialize(): void
    {
        foreach ($this->serializable->serializes() as $propertyName) {
            $property = $this->reflect()
                ->getProperty($propertyName);

            $this->serializations[$propertyName] = $this->handlerFactory
                ->getHandler($property)
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
            $property = $this
                ->reflect()
                ->getProperty($propertyName);

            $serialized = $this->serializations[$propertyName];

            $this->serializable->$propertyName = $this->handlerFactory
                ->getHandler($property)
                ->deserialize($serialized);
        }
    }
}
