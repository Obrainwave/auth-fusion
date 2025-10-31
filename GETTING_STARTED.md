# Getting Started with Auth Fusion

## 🚀 Quick Start in 5 Minutes

Follow these steps to get Auth Fusion running in your Laravel project.

### Step 1: Install the Package

```bash
composer require obrainwave/auth-fusion
```

### Step 2: Publish Configuration

```bash
php artisan vendor:publish --tag=auth-fusion-config
```

### Step 3: Choose Your Auth Driver

Edit your `.env` file:

```env
AUTH_FUSION_DRIVER=sanctum
```

**Driver Options:**
- `sanctum` - Recommended for most APIs
- `jwt` - For stateless JWT tokens
- `passport` - For OAuth2 applications

### Step 4: Install Your Chosen Driver

**✨ Easy Way - Use our installer command:**

```bash
# Install Sanctum driver
php artisan auth-fusion:install sanctum

# Install JWT driver
php artisan auth-fusion:install jwt

# Install Passport driver
php artisan auth-fusion:install passport

# Install all drivers at once
php artisan auth-fusion:install all
```

**📝 Manual Installation:**

#### Option A: Sanctum (Recommended)

```bash
composer require laravel/sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate

# Add to your User model
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    // ...
}
```

#### Option B: JWT

```bash
composer require tymon/jwt-auth
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"
php artisan jwt:secret
```

#### Option C: Passport

```bash
composer require laravel/passport
php artisan migrate
php artisan passport:install

# Add HasApiTokens to User model (same as Sanctum)
```

### Step 5: Use Auth Fusion!

That's it! You're ready to use Auth Fusion:

```php
use Obrainwave\AuthFusion\Facades\AuthFusion;

// Login
$result = AuthFusion::driver()->login([
    'email' => 'user@example.com',
    'password' => 'password'
]);

return response()->json([
    'token' => $result['token'],
    'user' => $result['user']
]);
```

## 📝 Complete Example

Here's a complete controller example:

```php
<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Obrainwave\AuthFusion\Facades\AuthFusion;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $result = AuthFusion::driver()->login(
                $request->only(['email', 'password'])
            );
            
            return response()->json([
                'message' => 'Login successful',
                'token' => $result['token'],
                'user' => $result['user']
            ]);
        } catch (\Illuminate\Auth\AuthenticationException $e) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }
    }
    
    public function logout(Request $request)
    {
        $token = $request->bearerToken();
        
        if ($token && AuthFusion::driver()->logout($token)) {
            return response()->json(['message' => 'Logged out']);
        }
        
        return response()->json(['message' => 'Logout failed'], 400);
    }
    
    public function me(Request $request)
    {
        $token = $request->bearerToken();
        $user = AuthFusion::driver()->getUser($token);
        
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        
        return response()->json(['user' => $user]);
    }
}
```

Add routes in `routes/api.php`:

```php
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::get('/me', [AuthController::class, 'me']);
```

## 🧪 Test It

```bash
# Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"your-email@example.com","password":"your-password"}'

# Get user (use token from login response)
curl -X GET http://localhost:8000/api/me \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"

# Logout
curl -X POST http://localhost:8000/api/logout \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## ✅ Verification Checklist

- [x] Package installed: `composer require auth-fusion/auth-fusion`
- [x] Config published: `php artisan vendor:publish --tag=auth-fusion-config`
- [x] Driver chosen in `.env`: `AUTH_FUSION_DRIVER=sanctum`
- [x] Auth driver installed (Sanctum/JWT/Passport)
- [x] User model updated (if using Sanctum)
- [x] Routes created
- [x] Controller created
- [x] Tested with curl or Postman

## 🎯 Next Steps

Now that you have Auth Fusion working:

1. **Read Documentation**
   - [Quick Reference](QUICK_REFERENCE.md) - Common patterns
   - [Usage Examples](USAGE_EXAMPLES.md) - Advanced scenarios
   - [Package Overview](PACKAGE_OVERVIEW.md) - Understanding architecture

2. **Explore Features**
   - Try switching drivers: `AuthFusion::driver('jwt')->login(...)`
   - Create a custom driver
   - Build middleware for your app

3. **Production Ready**
   - Set up rate limiting
   - Configure CORS
   - Implement refresh tokens
   - Add error handling

## 🆘 Need Help?

- **Documentation**: Check the [README](README.md)
- **Installation Issues**: See [INSTALLATION.md](INSTALLATION.md)
- **Examples**: Browse [USAGE_EXAMPLES.md](USAGE_EXAMPLES.md)
- **Questions**: Open a GitHub issue

## 🎉 Congratulations!

You've successfully installed and configured Auth Fusion!

The package is now ready to unify all your authentication needs in a single, flexible API.

Happy coding! 🚀

