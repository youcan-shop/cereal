<?php

namespace YouCan\Cereal;

use YouCan\Cereal\Contracts\SerializationHandler;
use YouCan\Cereal\SerializationHandlers\ScalarTypesSerializationHandler;

class SerializationHandlerFactory implements Contracts\SerializationHandlerFactory
{
    private array $handlers = [];

    private static ?SerializationHandlerFactory $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): SerializationHandlerFactory
    {
        if (self::$instance === null) {
            self::$instance = new SerializationHandlerFactory();
        }

        return self::$instance;
    }

    public function getHandler(string $type): SerializationHandler
    {
        if (!isset($this->handlers[$type])) {
            throw new \InvalidArgumentException(sprintf('cannot find handler for type "%s"', $type));
        }

        return $this->handlers[$type];
    }

    public function addHandler(string $type, SerializationHandler $handler): void
    {
        $this->handlers[$type] = $handler;
    }

    public function addHandlers(array $handlers): void
    {
        foreach ($handlers as $type => $handler) {
            $this->addHandler($type, $handler);
        }
    }
}
