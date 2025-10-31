# Auth Fusion 🔐

[![Latest Version on Packagist](https://img.shields.io/packagist/v/obrainwave/auth-fusion.svg?style=flat-square)](https://packagist.org/packages/obrainwave/auth-fusion)
[![Total Downloads](https://img.shields.io/packagist/dt/obrainwave/auth-fusion.svg?style=flat-square)](https://packagist.org/packages/obrainwave/auth-fusion)
[![License](https://img.shields.io/packagist/l/obrainwave/auth-fusion.svg?style=flat-square)](LICENSE.md)

A unified authentication package for Laravel that seamlessly integrates **Sanctum**, **JWT**, and **Passport** into a single, flexible API.

## Features

- 🎯 **Unified API** - Single interface for all authentication drivers
- 🔄 **Driver Swapping** - Switch between Sanctum, JWT, and Passport on the fly
- 🔧 **Flexible** - Use the driver that best fits your needs
- 📦 **Laravel 10-12** - Full support for Laravel 10, 11, and 12
- 🐘 **PHP 8.0+** - Modern PHP support
- 🎨 **Extensible** - Create custom drivers with ease

## Requirements

- PHP 8.0 or higher
- Laravel 10, 11, or 12
- One or more of the following authentication packages:
  - [Laravel Sanctum](https://laravel.com/docs/sanctum)
  - [tymon/jwt-auth](https://github.com/tymondesigns/jwt-auth)
  - [Laravel Passport](https://laravel.com/docs/passport)

## Installation

```bash
composer require obrainwave/auth-fusion
```

For Laravel 11+, the service provider and aliases will be auto-discovered. For Laravel 10, manually register in `config/app.php` if needed.

Publish the configuration file:

```bash
php artisan vendor:publish --tag=auth-fusion-config
```

### Easy Driver Installation

Use our artisan command to automatically install and configure drivers:

```bash
# Install Sanctum driver
php artisan auth-fusion:install sanctum

# Install JWT driver  
php artisan auth-fusion:install jwt

# Install Passport driver
php artisan auth-fusion:install passport

# Install all drivers at once
php artisan auth-fusion:install all

# Install with custom model name
php artisan auth-fusion:install sanctum --model=Admin
```

The command will:
- ✅ Install the authentication package via composer
- ✅ Publish configuration files
- ✅ Run necessary migrations
- ✅ Add required traits to your User model
- ✅ Configure `.env` automatically

Configure your default driver in `.env`:

```env
AUTH_FUSION_DRIVER=sanctum
```

## Quick Start

### Basic Usage

```php
use AuthFusion;

// Or use the full namespace:
// use Obrainwave\AuthFusion\Facades\AuthFusion;

// Login with default driver
$result = AuthFusion::driver()->login([
    'email' => 'user@example.com',
    'password' => 'password'
]);

return response()->json([
    'token' => $result['token'],
    'user' => $result['user']
]);

// Validate token
if (AuthFusion::driver()->validate($token)) {
    $user = AuthFusion::driver()->getUser($token);
}

// Logout
AuthFusion::driver()->logout($token);
```

### Using Specific Drivers

```php
// Use Sanctum
AuthFusion::driver('sanctum')->login($credentials);

// Use JWT
AuthFusion::driver('jwt')->login($credentials);

// Use Passport
AuthFusion::driver('passport')->login($credentials);
```

### Advanced Usage

#### Sanctum with Custom Options

```php
$result = AuthFusion::driver('sanctum')->login(
    $credentials,
    [
        'device_name' => 'iPhone 15',
        'abilities' => ['read', 'write'],
        'expires_at' => now()->addWeek()
    ]
);
```

#### JWT Token Refresh

```php
try {
    $newToken = AuthFusion::driver('jwt')->refresh($oldToken);
    return response()->json(['token' => $newToken['token']]);
} catch (\Illuminate\Auth\AuthenticationException $e) {
    return response()->json(['error' => 'Invalid token'], 401);
}
```

#### Switching Default Driver

```php
// Change default driver at runtime
AuthFusion::setDefaultDriver('jwt');

// Now all calls use JWT by default
AuthFusion::driver()->login($credentials);
```

## Available Methods

All drivers implement the `AuthDriverInterface` which provides:

| Method | Description |
|--------|-------------|
| `login(array $credentials, array $options = [])` | Authenticate user and return token |
| `logout($token)` | Logout user and invalidate token |
| `refresh($token)` | Refresh an access token |
| `validate($token)` | Check if token is valid |
| `getUser($token)` | Get authenticated user from token |
| `getName()` | Get the driver name |

## Driver-Specific Features

### Sanctum

- ✅ Plain text token support
- ✅ Token abilities/scopes
- ✅ Custom expiration times
- ✅ Device name tracking
- ⚠️ No built-in refresh (creates new token)

### JWT

- ✅ Stateless tokens
- ✅ Token refresh support
- ✅ Token invalidation
- ✅ Full JWT feature set

### Passport

- ✅ OAuth2 support
- ✅ Token scopes
- ✅ Client credentials
- ⚠️ Refresh requires OAuth2 flow

## Creating Custom Drivers

You can create custom drivers by extending the `AuthDriverInterface`:

```php
<?php

namespace App\Drivers;

use Obrainwave\AuthFusion\Contracts\AuthDriverInterface;

class CustomDriver implements AuthDriverInterface
{
    // Implement all required methods
    public function login(array $credentials, array $options = []): array { }
    public function logout($token): bool { }
    public function refresh($token): array { }
    public function validate($token): bool { }
    public function getUser($token): ?Authenticatable { }
    public function getName(): string { }
}
```

Register your custom driver:

```php
use App\Drivers\CustomDriver;
use AuthFusion;

AuthFusion::extend('custom', function () {
    return new CustomDriver();
});

// Use it
AuthFusion::driver('custom')->login($credentials);
```

## Error Handling

All drivers throw `\Illuminate\Auth\AuthenticationException` on authentication failures:

```php
try {
    $result = AuthFusion::driver()->login($credentials);
} catch (\Illuminate\Auth\AuthenticationException $e) {
    return response()->json([
        'message' => 'Invalid credentials'
    ], 401);
}
```

## Middleware

You can create unified middleware for all drivers:

```php
<?php

namespace App\Http\Middleware;

use AuthFusion;
use Closure;

class AuthFusionMiddleware
{
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();
        
        if (!$token || !AuthFusion::driver()->validate($token)) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        
        $user = AuthFusion::driver()->getUser($token);
        $request->setUserResolver(fn() => $user);
        
        return $next($request);
    }
}
```

## Configuration

Edit `config/auth-fusion.php`:

```php
return [
    'driver' => env('AUTH_FUSION_DRIVER', 'sanctum'),
    
    'drivers' => [
        'sanctum' => [
            'guard' => 'web',
        ],
        'jwt' => [
            'guard' => 'api',
        ],
        'passport' => [
            'guard' => 'web',
        ],
    ],
];
```

## Testing

Run tests with:

```bash
php artisan test
```

Or with PHPUnit:

```bash
./vendor/bin/phpunit
```

## Troubleshooting

### "Sanctum is not installed" Error

If you get this error even after installing Sanctum:

1. **Run composer dump-autoload:**
   ```bash
   composer dump-autoload
   ```

2. **Ensure your User model uses HasApiTokens:**
   ```php
   use Laravel\Sanctum\HasApiTokens;
   
   class User extends Authenticatable
   {
       use HasApiTokens;
   }
   ```

3. **Clear Laravel caches:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

4. **Verify Sanctum is installed:**
   ```bash
   composer show laravel/sanctum
   ```

### Similar Errors for JWT or Passport

Follow the same steps, but ensure you've:
- Published the configuration files
- Run necessary migrations
- Generated JWT secret (for JWT) or OAuth clients (for Passport)

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Credits

- Built with ❤️ for the Laravel community
- Inspired by the need for flexible authentication in modern applications

## Documentation

- 📖 [Getting Started](GETTING_STARTED.md) - Quick setup guide
- 📦 [Installation Guide](INSTALLATION.md) - Detailed installation instructions
- 💡 [Usage Examples](USAGE_EXAMPLES.md) - Practical code examples
- 🎯 [Quick Reference](QUICK_REFERENCE.md) - Common patterns and cheat sheet
- 🏗️ [Package Overview](PACKAGE_OVERVIEW.md) - Architecture and design details
- ⚙️ [Commands](COMMANDS.md) - Artisan commands reference
- 🤝 [Contributing](CONTRIBUTING.md) - How to contribute

## Support

For issues and questions:
- Open an issue on [GitHub](https://github.com/yourusername/auth-fusion)
- Check the documentation above
- Join our discussions

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

