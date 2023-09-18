<?php

namespace YouCanShop\Cereal;

use YouCanShop\Cereal\Contracts\SerializationHandler;

/** @phpstan-consistent-constructor */
class SerializationHandlerFactory implements Contracts\SerializationHandlerFactory
{
    private static ?self $instance = null;

    /** @var array<string, SerializationHandler> */
    private array $handlers = [];

    private function __construct()
    {
    }

    public static function getInstance(): self
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    public function getHandler(string $type): SerializationHandler
    {
        if (!isset($this->handlers[$type])) {
            return new DefaultSerializationHandler();
        }

        return $this->handlers[$type];
    }

    /**
     * @param array<string, SerializationHandler> $handlers
     */
    public function addHandlers(array $handlers): void
    {
        foreach ($handlers as $type => $handler) {
            $this->addHandler($type, $handler);
        }
    }

    public function addHandler(string $type, SerializationHandler $handler): void
    {
        $this->handlers[$type] = $handler;
    }
}
