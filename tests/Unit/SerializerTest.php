<?php

namespace Tests\Unit;

use Tests\Helpers\Cloner;
use Tests\Helpers\SomethingHandler;
use YouCanShop\Cereal\Cereal;
use YouCanShop\Cereal\Contracts\Serializable;
use YouCanShop\Cereal\Contracts\SerializationHandler;
use YouCanShop\Cereal\SerializationHandlerFactory;

it('serializes scalar types', function () {
    class User implements Serializable
    {
        use Cereal;

        public string $name;
        public int $age;
        public float $weight;
        public bool $isStraight;

        public function __construct(string $name, int $age, float $weight, bool $isStraight)
        {
            $this->name = $name;
            $this->age = $age;
            $this->weight = $weight;
            $this->isStraight = $isStraight;
        }

        public function serializes(): array
        {
            return ['name', 'age', 'weight', 'isStraight'];
        }
    }

    $user = new User('Aymane', 23, 65.0, true);
    $serializedUser = serialize($user);
    $deserializedUser = unserialize($serializedUser);

    expect($deserializedUser->name)->toBe($user->name);
    expect($deserializedUser->age)->toBe($user->age);
    expect($deserializedUser->weight)->toBe($user->weight);
    expect($deserializedUser->isStraight)->toBe($user->isStraight);
});


it('serializes classes', function () {
    class Something
    {
        private string $id;

        public function __construct(string $id)
        {
            $this->id = $id;
        }

        public function getId(): string
        {
            return $this->id;
        }
    }

    /** @var array<string, Something> $lookupTable */
    $lookupTable = [
        '1' => new Something('1'),
    ];

    $handler = new class($lookupTable) implements SerializationHandler {

        /** @var array<string, Something> */
        private array $lookupTable;

        /**
         * @param array<string, Something> $lookupTable
         */
        public function __construct(array $lookupTable)
        {
            $this->lookupTable = $lookupTable;
        }

        /**
         * @param Serializable $serializable
         * @param Something $value
         *
         * @return string
         */
        public function serialize(Serializable $serializable, $value): string
        {
            return $value->getId();
        }

        /**
         * @param Serializable $serializable
         * @param string $value
         *
         * @return Something
         */
        public function deserialize(Serializable $serializable, $value): Something
        {
            return $this->lookupTable[$value];
        }
    };

    SerializationHandlerFactory::getInstance()
        ->addHandler(Something::class, $handler);

    class Wrapper implements Serializable
    {
        use Cereal;

        public Something $thing;

        public function __construct(Something $thing)
        {
            $this->thing = $thing;
        }

        public function serializes(): array
        {
            return ['thing'];
        }
    }

    $wrapper = new Wrapper($lookupTable['1']);

    $serialized = serialize($wrapper);
    $deserialized = unserialize($serialized);

    expect($deserialized->thing->getId())
        ->toEqual($wrapper->thing->getId());
});

it('respects the serialization order', function () {
    class Number
    {
        public int $value;

        public function __construct(int $value)
        {
            $this->value = $value;
        }
    }

    class SomeList implements Serializable
    {
        use Cereal;

        public Number $first;
        public Number $second;
        public Number $third;

        public function __construct(Number $first, Number $second, Number $third)
        {
            $this->first = $first;
            $this->second = $second;
            $this->third = $third;
        }

        public function serializes(): array
        {
            return ['first', 'second', 'third'];
        }
    }

    class Handler implements SerializationHandler
    {
        /**
         * @param Serializable $serializable
         * @param Number $value
         *
         * @return mixed
         */
        public function serialize(Serializable $serializable, $value)
        {
            return $value->value;
        }

        /**
         * @param Serializable $serializable
         * @param int $value
         *
         * @return Number
         */
        public function deserialize(Serializable $serializable, $value): Number
        {
            if ($value === 4) {
                expect($serializable->first)
                    ->toBeInstanceOf(Number::class)
                    ->and($serializable->first->value)
                    ->toEqual(2);
            }

            return new Number($value);
        }
    }

    SerializationHandlerFactory::getInstance()
        ->addHandler(Number::class, new Handler);

    $list = new SomeList(
        new Number(2),
        new Number(4),
        new Number(8)
    );

    $serialized = serialize($list);
    unserialize($serialized);
});

it('keeps the same serializable reference', function () {
    $table = [
        'one' => new \Tests\Helpers\Something('one'),
        'two' => new \Tests\Helpers\Something('two'),
    ];

    SerializationHandlerFactory::getInstance()
        ->addHandler(
            \Tests\Helpers\Something::class,
            new SomethingHandler($table)
        );

    $wrapper = new \Tests\Helpers\Wrapper(reset($table));

    $cloner = new Cloner([$wrapper]);

    $serialized = serialize(clone $cloner);

    $deserialized = unserialize($serialized);

    $wrapper = $deserialized->data[0];

    $srProp = (new \ReflectionObject($wrapper))
        ->getProperty('serializer');

    $srProp->setAccessible(true);
    $serializer = $srProp->getValue($wrapper);

    $sbProp = (new \ReflectionObject($serializer))
        ->getProperty('serializable');

    $sbProp->setAccessible(true);
    $serializable = $sbProp->getValue($serializer);

    expect($wrapper->something)
        ->toBeInstanceOf(\Tests\Helpers\Something::class)
        ->and(spl_object_id($wrapper))
        ->toEqual(spl_object_id($serializable));
});
