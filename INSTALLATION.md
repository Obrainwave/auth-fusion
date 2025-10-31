# Installation Guide

## System Requirements

- PHP 8.0 or higher
- Laravel 10, 11, or 12
- Composer

## Step 1: Install via Composer

```bash
composer require obrainwave/auth-fusion
```

For Laravel 11+, the package will be auto-discovered. For Laravel 10, you may need to manually register the service provider in `config/app.php`:

```php
'providers' => [
    // ...
    AuthFusion\AuthFusionServiceProvider::class,
],
```

## Step 2: Publish Configuration

```bash
php artisan vendor:publish --tag=auth-fusion-config
```

This will create `config/auth-fusion.php` in your Laravel project.

## Step 3: Install Your Desired Auth Driver

Auth Fusion supports three drivers. Install at least one:

### Option A: Laravel Sanctum (Recommended for API)

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

Add to your `config/sanctum.php`:

```php
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
    '%s%s',
    'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
    Sanctum::currentApplicationUrlWithPort()
))),
```

### Option B: JWT (tymon/jwt-auth)

```bash
composer require tymon/jwt-auth
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
php artisan jwt:secret
```

### Option C: Laravel Passport (OAuth2)

```bash
composer require laravel/passport
php artisan migrate
php artisan passport:install
```

## Step 4: Configure Environment

Add to your `.env` file:

```env
AUTH_FUSION_DRIVER=sanctum
```

Available drivers: `sanctum`, `jwt`, `passport`

## Step 5: Update User Model (If Using Sanctum)

Add the `HasApiTokens` trait to your User model:

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;

    // ... rest of your model
}
```

## Step 6: Create Routes

Add to `routes/api.php`:

```php
use Obrainwave\AuthFusion\Facades\AuthFusion;

Route::post('/login', function (Request $request) {
    try {
        $result = AuthFusion::driver()->login(
            $request->only(['email', 'password'])
        );
        
        return response()->json($result);
    } catch (\Illuminate\Auth\AuthenticationException $e) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
```

## Step 7: Test the Installation

Test with curl or Postman:

```bash
curl -X POST http://your-app.test/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"user@example.com","password":"password"}'
```

## Verification Checklist

- ✅ Package installed via composer
- ✅ Configuration published
- ✅ At least one auth driver installed
- ✅ `.env` configured with `AUTH_FUSION_DRIVER`
- ✅ User model updated (for Sanctum)
- ✅ Routes created and tested

## Next Steps

- Read the [README.md](README.md) for usage examples
- Check [USAGE_EXAMPLES.md](USAGE_EXAMPLES.md) for advanced scenarios
- Create custom drivers if needed

## Troubleshooting

### "Driver not found" Error

Make sure you've:
1. Installed the auth driver (Sanctum/JWT/Passport)
2. Run necessary migrations
3. Configured the driver properly

### "Class not found" Errors

Run:
```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

### Sanctum Issues

For Sanctum, ensure:
- `HasApiTokens` trait is in User model
- `EnsureFrontendRequestsAreStateful` middleware is configured
- Database migrations have run

### JWT Issues

For JWT, ensure:
- `jwt:secret` command has been run
- JWT configuration is published
- User model implements `JWTSubject`

### Passport Issues

For Passport, ensure:
- OAuth clients created with `passport:install`
- Routes are using `throttle` middleware
- `HasApiTokens` is in User model

## Support

If you encounter issues:
1. Check the documentation
2. Search existing GitHub issues
3. Create a new issue with details

