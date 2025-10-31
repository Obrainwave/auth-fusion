# Usage Examples

This document provides practical examples of using Auth Fusion in your Laravel application.

## Table of Contents

- [Basic Authentication](#basic-authentication)
- [Controller Examples](#controller-examples)
- [Middleware Examples](#middleware-examples)
- [Multiple Drivers](#multiple-drivers)
- [Custom Drivers](#custom-drivers)
- [Testing Examples](#testing-examples)

## Basic Authentication

### Login Controller

```php
<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Obrainwave\AuthFusion\Facades\AuthFusion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = AuthFusion::driver()->login($request->only(['email', 'password']));
            
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

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        $token = $request->bearerToken();
        
        if ($token && AuthFusion::driver()->logout($token)) {
            return response()->json([
                'message' => 'Logout successful'
            ]);
        }
        
        return response()->json([
            'message' => 'Logout failed'
        ], 400);
    }

    /**
     * Get current authenticated user.
     */
    public function me(Request $request)
    {
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        
        $user = AuthFusion::driver()->getUser($token);
        
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        
        return response()->json(['user' => $user]);
    }
}
```

### Refresh Token Endpoint

```php
<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Obrainwave\AuthFusion\Facades\AuthFusion;
use Illuminate\Http\Request;

class TokenController extends Controller
{
    /**
     * Refresh access token.
     */
    public function refresh(Request $request)
    {
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json(['message' => 'Token required'], 400);
        }
        
        try {
            $result = AuthFusion::driver()->refresh($token);
            
            return response()->json([
                'token' => $result['token']
            ]);
        } catch (\Illuminate\Auth\AuthenticationException $e) {
            return response()->json([
                'message' => 'Invalid or expired token'
            ], 401);
        }
    }
}
```

## Controller Examples

### With Sanctum Specific Features

```php
<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Obrainwave\AuthFusion\Facades\AuthFusion;
use Illuminate\Http\Request;

class SanctumAuthController extends Controller
{
    public function login(Request $request)
    {
        $result = AuthFusion::driver('sanctum')->login(
            $request->only(['email', 'password']),
            [
                'device_name' => $request->input('device_name', $request->userAgent()),
                'abilities' => ['read', 'write'],
            ]
        );
        
        return response()->json($result);
    }
}
```

### With JWT Specific Features

```php
<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Obrainwave\AuthFusion\Facades\AuthFusion;
use Illuminate\Http\Request;

class JWTAuthController extends Controller
{
    public function login(Request $request)
    {
        $result = AuthFusion::driver('jwt')->login(
            $request->only(['email', 'password'])
        );
        
        return response()->json($result);
    }
    
    public function refresh(Request $request)
    {
        // JWT supports refresh tokens natively
        $result = AuthFusion::driver('jwt')->refresh(
            $request->bearerToken()
        );
        
        return response()->json($result);
    }
}
```

## Middleware Examples

### Unified Authentication Middleware

```php
<?php

namespace App\Http\Middleware;

use Obrainwave\AuthFusion\Facades\AuthFusion;
use Closure;
use Illuminate\Http\Request;

class AuthenticateWithAuthFusion
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        
        if (!$token) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        
        if (!AuthFusion::driver()->validate($token)) {
            return response()->json(['message' => 'Invalid token'], 401);
        }
        
        $user = AuthFusion::driver()->getUser($token);
        
        // Set user on request
        auth()->setUser($user);
        
        return $next($request);
    }
}
```

### Optional Authentication Middleware

```php
<?php

namespace App\Http\Middleware;

use Obrainwave\AuthFusion\Facades\AuthFusion;
use Closure;
use Illuminate\Http\Request;

class OptionalAuth
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        
        if ($token && AuthFusion::driver()->validate($token)) {
            $user = AuthFusion::driver()->getUser($token);
            auth()->setUser($user);
        }
        
        return $next($request);
    }
}
```

Register middleware in `app/Http/Kernel.php`:

```php
protected $middlewareAliases = [
    'auth.fusion' => \App\Http\Middleware\AuthenticateWithAuthFusion::class,
    'auth.optional' => \App\Http\Middleware\OptionalAuth::class,
];
```

## Multiple Drivers

### Switching Drivers at Runtime

```php
<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use Obrainwave\AuthFusion\Facades\AuthFusion;
use Illuminate\Http\Request;

class MultiDriverAuthController extends Controller
{
    public function login(Request $request)
    {
        $driver = $request->input('driver', 'sanctum');
        
        $result = AuthFusion::driver($driver)->login(
            $request->only(['email', 'password'])
        );
        
        return response()->json([
            'driver' => $driver,
            'token' => $result['token'],
            'user' => $result['user']
        ]);
    }
    
    public function validate(Request $request, string $driver)
    {
        $token = $request->bearerToken();
        
        $isValid = AuthFusion::driver($driver)->validate($token);
        
        return response()->json([
            'driver' => $driver,
            'valid' => $isValid
        ]);
    }
}
```

### Route Examples with Multiple Drivers

```php
use Illuminate\Support\Facades\Route;

// Sanctum routes
Route::prefix('sanctum')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])
        ->middleware('auth.fusion');
});

// JWT routes
Route::prefix('jwt')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
});

// Passport routes
Route::prefix('oauth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
});
```

## Custom Drivers

### Creating a Custom API Key Driver

```php
<?php

namespace App\Drivers;

use Obrainwave\AuthFusion\Contracts\AuthDriverInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Models\ApiToken;

class ApiKeyDriver implements AuthDriverInterface
{
    public function login(array $credentials, array $options = []): array
    {
        $apiKey = $credentials['api_key'] ?? null;
        
        if (!$apiKey) {
            throw new \Illuminate\Auth\AuthenticationException('API key required');
        }
        
        $token = ApiToken::where('key', hash('sha256', $apiKey))->first();
        
        if (!$token || !$token->is_valid) {
            throw new \Illuminate\Auth\AuthenticationException('Invalid API key');
        }
        
        return [
            'token' => $apiKey,
            'user' => $token->user,
        ];
    }
    
    public function logout($token): bool
    {
        $hashed = hash('sha256', $token);
        ApiToken::where('key', $hashed)->update(['is_valid' => false]);
        return true;
    }
    
    public function refresh($token): array
    {
        throw new \BadMethodCallException('API key refresh not supported');
    }
    
    public function validate($token): bool
    {
        return $this->getUser($token) !== null;
    }
    
    public function getUser($token): ?Authenticatable
    {
        $hashed = hash('sha256', $token);
        $tokenModel = ApiToken::where('key', $hashed)
            ->where('is_valid', true)
            ->first();
        
        return $tokenModel ? $tokenModel->user : null;
    }
    
    public function getName(): string
    {
        return 'apikey';
    }
}
```

Register the custom driver in `AppServiceProvider`:

```php
<?php

namespace App\Providers;

use App\Drivers\ApiKeyDriver;
use Obrainwave\AuthFusion\Facades\AuthFusion;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        AuthFusion::extend('apikey', function () {
            return new ApiKeyDriver();
        });
    }
}
```

Use the custom driver:

```php
$result = AuthFusion::driver('apikey')->login([
    'api_key' => 'your-api-key'
]);
```

## Testing Examples

### Feature Tests

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Obrainwave\AuthFusion\Facades\AuthFusion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthFusionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_sanctum()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password')
        ]);
        
        $result = AuthFusion::driver('sanctum')->login([
            'email' => 'test@example.com',
            'password' => 'password'
        ]);
        
        $this->assertNotNull($result['token']);
        $this->assertEquals($user->id, $result['user']->id);
    }
    
    public function test_token_validation_works()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password')
        ]);
        
        $result = AuthFusion::driver('sanctum')->login([
            'email' => $user->email,
            'password' => 'password'
        ]);
        
        $this->assertTrue(
            AuthFusion::driver('sanctum')->validate($result['token'])
        );
        
        $authenticatedUser = AuthFusion::driver('sanctum')->getUser($result['token']);
        $this->assertEquals($user->id, $authenticatedUser->id);
    }
    
    public function test_logout_invalidates_token()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password')
        ]);
        
        $result = AuthFusion::driver('sanctum')->login([
            'email' => $user->email,
            'password' => 'password'
        ]);
        
        $this->assertTrue(
            AuthFusion::driver('sanctum')->logout($result['token'])
        );
        
        $this->assertFalse(
            AuthFusion::driver('sanctum')->validate($result['token'])
        );
    }
}
```

## Advanced Use Cases

### Per-Route Driver Selection

```php
Route::post('/api/v1/login', function (Request $request) {
    return AuthFusion::driver('sanctum')->login(
        $request->only(['email', 'password'])
    );
});

Route::post('/api/v2/login', function (Request $request) {
    return AuthFusion::driver('jwt')->login(
        $request->only(['email', 'password'])
    );
});
```

### Dynamic Driver Based on Request Headers

```php
Route::post('/api/login', function (Request $request) {
    $driver = $request->header('X-Auth-Driver', 'sanctum');
    
    return AuthFusion::driver($driver)->login(
        $request->only(['email', 'password'])
    );
});
```

### Migration from One Driver to Another

```php
public function migrateTokens(string $fromDriver, string $toDriver)
{
    // Get all active tokens from old driver
    $tokens = $this->getActiveTokensFromDriver($fromDriver);
    
    foreach ($tokens as $oldToken) {
        $user = AuthFusion::driver($fromDriver)->getUser($oldToken);
        
        if ($user) {
            // Create new token with new driver
            $result = AuthFusion::driver($toDriver)->login([
                'email' => $user->email,
                'password' => 'temporary-password' // You'll need actual credentials
            ]);
            
            // Notify user or update database with new token
            $this->updateTokenForUser($user, $result['token']);
        }
    }
}
```

