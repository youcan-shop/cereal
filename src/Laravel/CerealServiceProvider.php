<?php

namespace YouCan\Cereal\Laravel;

use Exception;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;
use YouCan\Cereal\Contracts\SerializationHandlerFactory as HandlerFactoryContract;
use YouCan\Cereal\SerializationHandlerFactory;

final class CerealServiceProvider extends ServiceProvider
{
    /**
     * @throws Exception
     */
    public function boot(): void
    {
        $path = realpath(__DIR__ . '/../../config/config.php');
        if (!is_string($path)) {
            throw new Exception('could not load default config');
        }

        $this->publishes([$path => config_path('cereal.php')]);
        $this->mergeConfigFrom($path, 'cereal');

        $handlerFactory = SerializationHandlerFactory::getInstance();

        $this->app->bind(
            HandlerFactoryContract::class,
            fn(): SerializationHandlerFactory => $handlerFactory
        );

        $handlers = $this->config('handlers');
        if (!is_array($handlers)) {
            throw new InvalidArgumentException('invalid handler configuration');
        }

        foreach ($handlers as $type => $handler) {
            $handlerFactory->addHandler($type, $handler);
        }
    }

    /**
     * @param string|null $key
     * @param mixed $default
     *
     * @return Repository|Application|mixed
     */
    private function config(?string $key = null, $default = null)
    {
        $key = $key === null ? '' : ".$key";

        return config('cereal' . $key, $default);
    }
}
