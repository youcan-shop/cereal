<?php

namespace Tests;

use YouCan\Cereal\Contracts\Serializable;
use YouCan\Cereal\Contracts\SerializationHandler;
use YouCan\Cereal\SerializationHandlerFactory;
use YouCan\Cereal\SerializeTrait;

it('serializes scalar types', function () {
    class User implements Serializable
    {
        use SerializeTrait;

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
            return ['fullname', 'age', 'weight', 'isStraight'];
        }
    }

    $user = new User('Aymane', 30, 80.5, true);
    $serializedUser = serialize($user);
    $deserializedUser = unserialize($serializedUser);

    expect($deserializedUser->fullname)->toBe($user->name);
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

    $thing = $lookupTable['1'];
    $serialized = serialize($thing);
    $deserialized = unserialize($serialized);

    expect($deserialized->getId())
        ->toEqual($thing->getId());
});
