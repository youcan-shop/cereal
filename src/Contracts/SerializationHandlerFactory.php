<?php

namespace YouCanShop\Cereal\Contracts;

interface SerializationHandlerFactory
{
    public function getHandler(string $type): SerializationHandler;

    public function addHandler(string $type, SerializationHandler $handler): void;

    /**
     * @param array<string, SerializationHandler> $handlers
     */
    public function addHandlers(array $handlers): void;
}
