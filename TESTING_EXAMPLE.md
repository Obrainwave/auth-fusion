# Testing Example for Auth Fusion

This document provides a complete example of setting up and testing Auth Fusion locally.

## Scenario: Testing in Laravel Project

Let's say you have a Laravel project at `C:\xampp\htdocs\my-project` and auth-fusion at `C:\xampp\htdocs\auth-fusion`.

### Step 1: Setup Path Repository

In your Laravel project's `composer.json`, add:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "../auth-fusion"
        }
    ],
    "require": {
        "obrainwave/auth-fusion": "@dev"
    }
}
```

### Step 2: Install the Package

```bash
cd C:\xampp\htdocs\my-project
composer require obrainwave/auth-fusion @dev
```

### Step 3: Install Sanctum

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

### Step 4: Update User Model

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

### Step 5: Clear Caches

```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

### Step 6: Test It

Create a route in `routes/api.php`:

```php
use AuthFusion;
use Illuminate\Http\Request;

Route::post('/test-login', function (Request $request) {
    try {
        $result = AuthFusion::driver()->login([
            'email' => $request->email,
            'password' => $request->password,
        ]);
        
        return response()->json($result);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});
```

Test with:
```bash
curl -X POST http://localhost:8000/api/test-login \
  -H "Content-Type: application/json" \
  -d '{"email":"your-email@example.com","password":"your-password"}'
```

## Common Pitfalls

### 1. Autoload Not Working

**Problem:** Classes not found even after installation

**Solution:**
```bash
composer dump-autoload
php artisan config:clear
```

### 2. Sanctum Classes Not Found

**Problem:** Error "Sanctum is not installed" even after installing

**Causes:**
1. Sanctum not in your Laravel project's `composer.json`
2. Autoloader not refreshed
3. User model missing `HasApiTokens` trait

**Solution:**
1. Check: `composer show laravel/sanctum`
2. Run: `composer dump-autoload`
3. Verify User model has `use HasApiTokens;`

### 3. Path Repository Issues

**Problem:** Package not loading from local path

**Solution:**
- Use relative path: `"url": "../auth-fusion"`
- Use absolute path: `"url": "C:/xampp/htdocs/auth-fusion"`
- Ensure path uses forward slashes on Windows

### 4. Version Constraint Issues

**Problem:** Composer won't install package

**Solution:**
- Use `@dev` constraint: `"obrainwave/auth-fusion": "@dev"`
- Or use `*`: `"obrainwave/auth-fusion": "*"`

## Debug Checklist

When testing fails, check:

- [ ] Auth Fusion package installed: `composer show obrainwave/auth-fusion`
- [ ] Sanctum installed: `composer show laravel/sanctum`
- [ ] User model has `HasApiTokens` trait
- [ ] Config published: `php artisan vendor:publish --tag=auth-fusion-config`
- [ ] .env has: `AUTH_FUSION_DRIVER=sanctum`
- [ ] Autoload refreshed: `composer dump-autoload`
- [ ] Caches cleared: `php artisan config:clear`
- [ ] Routes defined correctly
- [ ] Database migrated

## Additional Testing

### Test JWT Driver

1. Install JWT: `composer require tymon/jwt-auth`
2. Configure: `php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"`
3. Generate secret: `php artisan jwt:secret`
4. Switch driver: `AUTH_FUSION_DRIVER=jwt` in `.env`

### Test Passport Driver

1. Install Passport: `composer require laravel/passport`
2. Migrate: `php artisan migrate`
3. Install clients: `php artisan passport:install`
4. Add `HasApiTokens` to User model (same as Sanctum)
5. Switch driver: `AUTH_FUSION_DRIVER=passport` in `.env`

## Next Steps

After successful local testing:
1. Test all three drivers
2. Write integration tests
3. Test edge cases
4. Prepare for Packagist release

