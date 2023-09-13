<?php

namespace Tests;

use YouCan\Cereal\Contracts\Serializable;
use YouCan\Cereal\SerializeTrait;

it('serializes', function () {
    class User implements Serializable
    {
        use SerializeTrait;

        public string $fullname;
        public int $age;
        public float $weight;
        public bool $isStraight;

        public function __construct(string $fullname, int $age, float $weight, bool $isStraight)
        {
            $this->fullname = $fullname;
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

    expect($deserializedUser->fullname)->toBe($user->fullname);
    expect($deserializedUser->age)->toBe($user->age);
    expect($deserializedUser->weight)->toBe($user->weight);
    expect($deserializedUser->isStraight)->toBe($user->isStraight);
});
