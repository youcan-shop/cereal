<?php

namespace YouCanShop\Cereal;

use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use YouCanShop\Cereal\Contracts\Serializable;
use YouCanShop\Cereal\Contracts\SerializationHandlerFactory as SerializationHandlerFactoryContract;

final class Serializer
{
    private Serializable $serializable;

    /** @var class-string<SerializationHandlerFactoryContract> */
    private string $factoryClass;

    /**
     * this array matches property names to serializations
     * e.g. "store" => STORE_UUID
     *
     * @var array<string, mixed>
     */
    private array $serializations = [];

    /**
     * @param class-string<SerializationHandlerFactoryContract> $factoryClass
     */
    public function __construct(
        Serializable $serializable,
        string $factoryClass = SerializationHandlerFactory::class
    ) {
        $this->serializable = $serializable;
        $this->factoryClass = $factoryClass;
    }

    /**
     * @return string[]
     * @throws ReflectionException
     */
    public function __sleep(): array
    {
        $this->serialize();

        return [
            'serializations',
            'serializable',
            'factoryClass',
        ];
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

            $typeName = $type instanceof ReflectionNamedType
                ? $type->getName()
                : '__implicit';

            $this->serializations[$propertyName] = $this->getSerializationHandlerFactory()
                ->getHandler($typeName)
                ->serialize(
                    $this->serializable,
                    $this->serializable->$propertyName
                );
        }
    }

    /**
     * @return ReflectionClass<object>
     */
    private function reflect(): ReflectionClass
    {
        return new ReflectionClass($this->serializable);
    }

    public function getSerializationHandlerFactory(): SerializationHandlerFactoryContract
    {
        return call_user_func([$this->factoryClass, 'getInstance']);
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

            $typeName = $type instanceof ReflectionNamedType
                ? $type->getName()
                : '__implicit';

            $serialized = $this->serializations[$propertyName];

            $this->serializable->$propertyName = $this->getSerializationHandlerFactory()
                ->getHandler($typeName)
                ->deserialize(
                    $this->serializable,
                    $serialized
                );
        }
    }
}
