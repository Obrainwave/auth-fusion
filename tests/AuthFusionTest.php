<?php

namespace Obrainwave\AuthFusion\Tests;

use Orchestra\Testbench\TestCase;
use Obrainwave\AuthFusion\AuthFusionServiceProvider;
use Obrainwave\AuthFusion\Facades\AuthFusion;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthFusionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Run migrations if you have a test database
        // $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    /**
     * Get package providers.
     */
    protected function getPackageProviders($app)
    {
        return [
            AuthFusionServiceProvider::class,
        ];
    }

    /**
     * Get package aliases.
     */
    protected function getPackageAliases($app)
    {
        return [
            'AuthFusion' => \Obrainwave\AuthFusion\Facades\AuthFusion::class,
        ];
    }

    /**
     * Define environment setup.
     */
    protected function defineEnvironment($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        // Set auth fusion driver
        $app['config']->set('auth-fusion.driver', 'sanctum');
    }

    /**
     * Test service provider is loaded.
     */
    public function test_service_provider_is_loaded()
    {
        $this->assertTrue($this->app->bound('auth-fusion'));
    }

    /**
     * Test facade returns manager.
     */
    public function test_facade_returns_manager()
    {
        $manager = AuthFusion::getFacadeRoot();
        $this->assertInstanceOf(\Obrainwave\AuthFusion\Manager\AuthDriverManager::class, $manager);
    }

    /**
     * Test default driver can be retrieved.
     */
    public function test_default_driver_can_be_retrieved()
    {
        $driver = AuthFusion::getDefaultDriver();
        $this->assertIsString($driver);
    }

    /**
     * Test can set default driver.
     */
    public function test_can_set_default_driver()
    {
        AuthFusion::setDefaultDriver('jwt');
        $this->assertEquals('jwt', AuthFusion::getDefaultDriver());
    }

    /**
     * Test can get driver instance.
     */
    public function test_can_get_driver_instance()
    {
        $driver = AuthFusion::driver('sanctum');
        $this->assertInstanceOf(\Obrainwave\AuthFusion\Contracts\AuthDriverInterface::class, $driver);
    }

    /**
     * Test invalid driver throws exception.
     */
    public function test_invalid_driver_throws_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        AuthFusion::driver('invalid-driver');
    }

    /**
     * Test can extend with custom driver.
     */
    public function test_can_extend_with_custom_driver()
    {
        $customDriver = new class implements \Obrainwave\AuthFusion\Contracts\AuthDriverInterface {
            public function login(array $credentials, array $options = []): array {
                return ['token' => 'test', 'user' => null];
            }
            public function logout($token): bool { return true; }
            public function refresh($token): array { return ['token' => 'new']; }
            public function validate($token): bool { return true; }
            public function getUser($token) { return null; }
            public function getName(): string { return 'test'; }
        };

        AuthFusion::extend('test', fn() => $customDriver);
        $driver = AuthFusion::driver('test');
        $this->assertEquals('test', $driver->getName());
    }
}

