<?php

namespace Tests;

use Tests\Components\SerializationHandlerFactory;
use YouCan\Cereal\Contracts\Serializable;
use YouCan\Cereal\Serializer;

it('serializes', function () {
    class A implements Serializable
    {
        private Serializer $serializer;
        public string $property = 'hello';

        public function __construct()
        {
            $this->serializer = new Serializer(
                new SerializationHandlerFactory(),
                $this
            );
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

    $a = new A();
});
