<?php

namespace Obrainwave\AuthFusion\Manager;

use Obrainwave\AuthFusion\Contracts\AuthDriverInterface;
use Obrainwave\AuthFusion\Drivers\JWTDriver;
use Obrainwave\AuthFusion\Drivers\PassportDriver;
use Obrainwave\AuthFusion\Drivers\SanctumDriver;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Http\Request;

class AuthDriverManager
{
    /**
     * @var AuthFactory
     */
    protected $auth;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var array
     */
    protected $drivers = [];

    /**
     * @var array
     */
    protected $customCreators = [];

    /**
     * @var string
     */
    protected $defaultDriver;

    /**
     * Create a new AuthDriverManager instance.
     *
     * @param AuthFactory $auth
     * @param Request $request
     * @param string $defaultDriver
     */
    public function __construct(AuthFactory $auth, Request $request, string $defaultDriver = 'sanctum')
    {
        $this->auth = $auth;
        $this->request = $request;
        $this->defaultDriver = $defaultDriver;
    }

    /**
     * Get a driver instance.
     *
     * @param string|null $driver
     * @return AuthDriverInterface
     */
    public function driver(?string $driver = null): AuthDriverInterface
    {
        $driver = $driver ?? $this->getDefaultDriver();

        if (! isset($this->drivers[$driver])) {
            $this->drivers[$driver] = $this->createDriver($driver);
        }

        return $this->drivers[$driver];
    }

    /**
     * Create a new driver instance.
     *
     * @param string $driver
     * @return AuthDriverInterface
     */
    protected function createDriver(string $driver): AuthDriverInterface
    {
        // Check if a custom driver creator exists
        if (isset($this->customCreators[$driver])) {
            return $this->customCreators[$driver]();
        }

        // Create default drivers
        return match ($driver) {
            'sanctum' => new SanctumDriver($this->auth, $this->request),
            'jwt' => new JWTDriver($this->auth),
            'passport' => new PassportDriver($this->auth),
            default => throw new \InvalidArgumentException("Driver [{$driver}] is not supported."),
        };
    }

    /**
     * Register a custom driver creator.
     *
     * @param string $driver
     * @param callable $callback
     * @return $this
     */
    public function extend(string $driver, callable $callback): self
    {
        $this->customCreators[$driver] = $callback;

        return $this;
    }

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver(): string
    {
        return $this->defaultDriver;
    }

    /**
     * Set the default driver name.
     *
     * @param string $driver
     * @return $this
     */
    public function setDefaultDriver(string $driver): self
    {
        $this->defaultDriver = $driver;

        return $this;
    }

    /**
     * Get all of the created drivers.
     *
     * @return array
     */
    public function getDrivers(): array
    {
        return $this->drivers;
    }

    /**
     * Disconnect the given driver and remove from local cache.
     *
     * @param string|null $driver
     * @return $this
     */
    public function disconnectDriver(?string $driver = null): self
    {
        $driver = $driver ?? $this->getDefaultDriver();

        unset($this->drivers[$driver]);

        return $this;
    }

    /**
     * Disconnect all drivers.
     *
     * @return $this
     */
    public function disconnectAllDrivers(): self
    {
        $this->drivers = [];

        return $this;
    }
}

