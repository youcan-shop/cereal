<?php

namespace YouCanShop\Cereal;

use ArrayAccess;
use Traversable;
use YouCanShop\Cereal\Contracts\Serializable;
use YouCanShop\Cereal\Contracts\SerializationHandler;
use YouCanShop\Cereal\Contracts\SerializationHandlerFactory;

final class IterableSerializationHandler implements SerializationHandler
{
    private SerializationHandlerFactory $factory;

    public function __construct(SerializationHandlerFactory $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param ArrayAccess<array-key, mixed>&Traversable<array-key, mixed> $value
     *
     * @return array{iterable: string, types: array<array-key, string>}
     */
    public function serialize(Serializable $serializable, $value): array
    {
        $copy = clone $value;
        $types = [];

        foreach ($value as $key => $item) {
            $types[$key] = is_object($item) ? get_class($item) : '__implicit';
            $copy[$key] = $this->factory->getHandler($types[$key])->serialize($serializable, $item);
        }

        return ['iterable' => serialize($copy), 'types' => $types];
    }

    /**
     * @param array{iterable: string, types: array<array-key, string>}|string $value
     *
     * @return mixed
     */
    public function deserialize(Serializable $serializable, $value)
    {
        if (is_string($value)) {
            return unserialize($value);
        }

        /** @var ArrayAccess<array-key, mixed>&Traversable<array-key, mixed> $iterable */
        $iterable = unserialize($value['iterable']);

        foreach ($value['types'] as $key => $type) {
            $iterable[$key] = $this->factory->getHandler($type)->deserialize($serializable, $iterable[$key]);
        }

        return $iterable;
    }
}
