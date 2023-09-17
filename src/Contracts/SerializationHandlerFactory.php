<?php

namespace YouCanShop\Cereal\Contracts;

interface SerializationHandlerFactory
{
    public static function getInstance(): self;

    public function getHandler(string $type): SerializationHandler;
}
