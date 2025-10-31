# Quick Reference

## Installation

```bash
composer require obrainwave/auth-fusion
php artisan vendor:publish --tag=auth-fusion-config
```

Set in `.env`:
```env
AUTH_FUSION_DRIVER=sanctum
```

## Basic Usage

### Login
```php
use Obrainwave\AuthFusion\Facades\AuthFusion;

$result = AuthFusion::driver()->login([
    'email' => 'user@example.com',
    'password' => 'password'
]);
```

### Validate Token
```php
$isValid = AuthFusion::driver()->validate($token);
```

### Get User
```php
$user = AuthFusion::driver()->getUser($token);
```

### Logout
```php
AuthFusion::driver()->logout($token);
```

### Refresh Token
```php
$result = AuthFusion::driver()->refresh($token);
```

## Driver Switching

```php
// Use specific driver
AuthFusion::driver('sanctum')->login($credentials);
AuthFusion::driver('jwt')->login($credentials);
AuthFusion::driver('passport')->login($credentials);

// Change default
AuthFusion::setDefaultDriver('jwt');
```

## Advanced Usage

### Sanctum with Options
```php
$result = AuthFusion::driver('sanctum')->login($credentials, [
    'device_name' => 'iPhone',
    'abilities' => ['read', 'write'],
    'expires_at' => now()->addWeek()
]);
```

### JWT Refresh
```php
try {
    $result = AuthFusion::driver('jwt')->refresh($oldToken);
} catch (\Illuminate\Auth\AuthenticationException $e) {
    // Handle error
}
```

## Custom Driver

```php
class CustomDriver implements AuthDriverInterface
{
    public function login(array $credentials, array $options = []): array { }
    public function logout($token): bool { }
    public function refresh($token): array { }
    public function validate($token): bool { }
    public function getUser($token) { }
    public function getName(): string { return 'custom'; }
}

// Register
AuthFusion::extend('custom', fn() => new CustomDriver());
```

## Common Patterns

### Controller
```php
public function login(Request $request)
{
    try {
        $result = AuthFusion::driver()->login(
            $request->only(['email', 'password'])
        );
        return response()->json($result);
    } catch (\Illuminate\Auth\AuthenticationException $e) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }
}
```

### Middleware
```php
$token = $request->bearerToken();
if (AuthFusion::driver()->validate($token)) {
    $user = AuthFusion::driver()->getUser($token);
    auth()->setUser($user);
}
```

## Helper Functions

```php
use AuthFusion\Support\AuthFusionHelper;

AuthFusionHelper::user();           // Get current user
AuthFusionHelper::check();          // Check if authenticated
AuthFusionHelper::token();          // Get token from request
AuthFusionHelper::validate();       // Validate current token
```

## Configuration

`config/auth-fusion.php`:
```php
return [
    'driver' => env('AUTH_FUSION_DRIVER', 'sanctum'),
    'drivers' => [
        'sanctum' => ['guard' => 'web'],
        'jwt' => ['guard' => 'api'],
        'passport' => ['guard' => 'web'],
    ],
];
```

## Cheat Sheet

| Action | Code |
|--------|------|
| Login | `AuthFusion::driver()->login($credentials)` |
| Logout | `AuthFusion::driver()->logout($token)` |
| Validate | `AuthFusion::driver()->validate($token)` |
| Get User | `AuthFusion::driver()->getUser($token)` |
| Refresh | `AuthFusion::driver()->refresh($token)` |
| Switch Driver | `AuthFusion::driver('driver_name')->method()` |
| Change Default | `AuthFusion::setDefaultDriver('name')` |
| Custom Driver | `AuthFusion::extend('name', fn() => new Driver())` |

## Troubleshooting

| Issue | Solution |
|-------|----------|
| Driver not found | Install auth package (sanctum/jwt/passport) |
| Class not found | Run `composer dump-autoload` |
| Invalid credentials | Check email/password in database |
| Token invalid | Verify token format and expiration |

## Links

- 📖 [Full Documentation](README.md)
- 📦 [Installation Guide](INSTALLATION.md)
- 💡 [Usage Examples](USAGE_EXAMPLES.md)
- 🏗️ [Package Overview](PACKAGE_OVERVIEW.md)
- 🤝 [Contributing](CONTRIBUTING.md)
- 📝 [Changelog](CHANGELOG.md)

