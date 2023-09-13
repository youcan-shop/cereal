<?php

namespace Tests;

use YouCanShop\Cereal\Contracts\Serializable;
use YouCanShop\Cereal\Contracts\SerializationHandler;
use YouCanShop\Cereal\SerializationHandlerFactory;
use YouCanShop\Cereal\Cereal;

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
         * @param Something $value
         *
         * @return string
         */
        public function serialize($value): string
        {
            return $value->getId();
        }

        /**
         * @param string $value
         *
         * @return Something
         */
        public function deserialize($value): Something
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
