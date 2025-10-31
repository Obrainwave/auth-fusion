# Auth Fusion - Package Overview

## What is Auth Fusion?

Auth Fusion is a Laravel package that provides a **unified interface** for working with different authentication drivers (Sanctum, JWT, and Passport). It eliminates the need to rewrite code when switching between authentication systems.

## Core Concept

Instead of writing driver-specific code like:

```php
// Sanctum
$token = $user->createToken('app')->plainTextToken;

// JWT
$token = auth('api')->login($user);

// Passport
$token = $user->createToken('app')->accessToken;
```

You can now use a single API:

```php
// Works with ANY driver!
$result = AuthFusion::driver()->login($credentials);
```

## Architecture

### 1. Interface-Based Design

All drivers implement `AuthDriverInterface`, ensuring consistent API:

```php
interface AuthDriverInterface
{
    public function login(array $credentials, array $options = []): array;
    public function logout($token): bool;
    public function refresh($token): array;
    public function validate($token): bool;
    public function getUser($token): ?Authenticatable;
    public function getName(): string;
}
```

### 2. Manager Pattern

`AuthDriverManager` handles driver creation, caching, and switching:

```php
AuthFusion::driver('sanctum')->login($credentials);
AuthFusion::driver('jwt')->login($credentials);
AuthFusion::setDefaultDriver('passport');
```

### 3. Adapter Pattern

Each driver (`SanctumDriver`, `JWTDriver`, `PassportDriver`) adapts the specific authentication library to our unified interface.

## Package Structure

```
auth-fusion/
├── config/
│   └── auth-fusion.php          # Package configuration
├── src/
│   ├── AuthFusionServiceProvider.php  # Registers package
│   ├── Facades/
│   │   └── AuthFusion.php       # Main facade
│   ├── Contracts/
│   │   └── AuthDriverInterface.php   # Driver contract
│   ├── Drivers/
│   │   ├── SanctumDriver.php    # Sanctum adapter
│   │   ├── JWTDriver.php        # JWT adapter
│   │   └── PassportDriver.php   # Passport adapter
│   ├── Manager/
│   │   └── AuthDriverManager.php     # Driver factory/manager
│   └── Support/
│       ├── AuthFusionHelper.php # Helper functions
│       └── TokenData.php        # Token data object
├── tests/
│   └── AuthFusionTest.php       # Test suite
├── composer.json                # Dependencies
├── README.md                    # Main documentation
├── INSTALLATION.md              # Setup guide
├── USAGE_EXAMPLES.md            # Code examples
└── CHANGELOG.md                 # Version history
```

## Key Features

### ✅ Unified API

Single interface for all drivers - learn once, use everywhere.

### ✅ Dynamic Driver Switching

Change authentication system on the fly without code changes.

### ✅ Extensible

Create custom drivers implementing `AuthDriverInterface`.

### ✅ Laravel Native

Integrates seamlessly with Laravel's service container.

### ✅ Zero Breaking Changes

Can be added to existing projects without modifications.

## Use Cases

### 1. Multi-Driver Applications

Run multiple authentication systems simultaneously:

```php
// Use Sanctum for web
Route::post('/web/login', fn() => AuthFusion::driver('sanctum')->login(...));

// Use JWT for mobile
Route::post('/mobile/login', fn() => AuthFusion::driver('jwt')->login(...));
```

### 2. Easy Migration

Migrate from one auth system to another:

```php
// Change config/env and done!
AUTH_FUSION_DRIVER=jwt  # Switch from sanctum to jwt
```

### 3. A/B Testing

Test different auth systems with the same codebase.

### 4. Custom Drivers

Build your own authentication system:

```php
AuthFusion::extend('custom', fn() => new CustomDriver());
```

## Technical Compatibility

| Component | Versions |
|-----------|----------|
| Laravel | 10.x, 11.x, 12.x |
| PHP | 8.0+ |
| Sanctum | Latest |
| JWT | tymon/jwt-auth latest |
| Passport | Latest |

## How It Works

### 1. Service Registration

Package registers `AuthDriverManager` as singleton in service container:

```php
$this->app->singleton('auth-fusion', function ($app) {
    return new AuthDriverManager(...);
});
```

### 2. Driver Resolution

When you call `AuthFusion::driver()`, the manager:
1. Checks if driver already instantiated (cached)
2. Creates new driver if not cached
3. Caches and returns driver instance

### 3. Method Delegation

Facade delegates calls to the manager, which forwards to the specific driver:

```php
AuthFusion::driver()->login($credentials);
// ↳ Manager::driver()
//   ↳ SanctumDriver::login()
```

## Benefits Over Direct Usage

### Without Auth Fusion

```php
// Multiple implementations needed
if (config('auth.default') === 'sanctum') {
    $token = $user->createToken('app')->plainTextToken;
} elseif (config('auth.default') === 'jwt') {
    $token = auth('api')->login($user);
} else {
    $token = $user->createToken('app')->accessToken;
}
```

### With Auth Fusion

```php
// Single implementation
$result = AuthFusion::driver()->login($credentials);
```

## Performance

- **Zero overhead**: No extra database queries
- **Driver caching**: Same driver instance reused
- **Lazy loading**: Drivers created only when needed
- **Native speed**: Uses Laravel's auth system directly

## Security

- Uses Laravel's built-in security features
- No data storage of tokens or credentials
- Driver-specific security best practices
- Token validation delegated to actual auth libraries

## Testing

Built-in test suite ensures:
- All drivers work correctly
- Facade resolution works
- Custom drivers can be registered
- Manager caches properly

## Future Enhancements

Potential features:
- [ ] Built-in rate limiting
- [ ] Token rotation
- [ ] Multi-factor authentication
- [ ] Social auth drivers
- [ ] Redis session driver
- [ ] GraphQL support

## Getting Started

1. **Install**: `composer require obrainwave/auth-fusion`
2. **Configure**: `php artisan vendor:publish --tag=auth-fusion-config`
3. **Choose driver**: Set `AUTH_FUSION_DRIVER` in `.env`
4. **Use**: `AuthFusion::driver()->login($credentials)`

## Documentation

- **[README.md](README.md)**: Quick start and overview
- **[INSTALLATION.md](INSTALLATION.md)**: Detailed setup guide
- **[USAGE_EXAMPLES.md](USAGE_EXAMPLES.md)**: Code examples
- **[CHANGELOG.md](CHANGELOG.md)**: Version history
- **[CONTRIBUTING.md](CONTRIBUTING.md)**: Contribution guidelines

## Community

- 🐛 **Bug Reports**: GitHub Issues
- 💡 **Feature Requests**: GitHub Discussions
- ❓ **Questions**: GitHub Discussions
- 🤝 **Contributions**: Pull Requests welcome!

## License

MIT License - Free to use in any project.

---

Built with ❤️ for the Laravel community

