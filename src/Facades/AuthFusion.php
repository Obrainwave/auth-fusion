<?php

namespace Obrainwave\AuthFusion\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Obrainwave\AuthFusion\Contracts\AuthDriverInterface driver(string|null $driver = null)
 * @method static \Obrainwave\AuthFusion\Manager\AuthDriverManager extend(string $driver, callable $callback)
 * @method static string getDefaultDriver()
 * @method static \Obrainwave\AuthFusion\Manager\AuthDriverManager setDefaultDriver(string $driver)
 * @method static array getDrivers()
 * @method static \Obrainwave\AuthFusion\Manager\AuthDriverManager disconnectDriver(?string $driver = null)
 * @method static \Obrainwave\AuthFusion\Manager\AuthDriverManager disconnectAllDrivers()
 *
 * @see \Obrainwave\AuthFusion\Manager\AuthDriverManager
 */
class AuthFusion extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'auth-fusion';
    }
}

