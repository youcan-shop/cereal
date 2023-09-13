<?php

namespace YouCan\Cereal\Contracts;

interface SerializationHandlerFactory
{
    public function getHandler(string $type): SerializationHandler;

    public function addHandler(string $type, SerializationHandler $handler): void;

    public function addHandlers(array $handlers): void;
}
