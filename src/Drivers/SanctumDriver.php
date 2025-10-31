<?php

namespace Obrainwave\AuthFusion\Drivers;

use Obrainwave\AuthFusion\Contracts\AuthDriverInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Factory as AuthFactory;
use Illuminate\Http\Request;

class SanctumDriver implements AuthDriverInterface
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
     * Create a new SanctumDriver instance.
     *
     * @param AuthFactory $auth
     * @param Request $request
     */
    public function __construct(AuthFactory $auth, Request $request)
    {
        $this->auth = $auth;
        $this->request = $request;
    }

    /**
     * Authenticate user with credentials.
     *
     * @param array $credentials
     * @param array $options
     * @return array ['token' => string, 'user' => Authenticatable]
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function login(array $credentials, array $options = []): array
    {
        // Attempt authentication
        if (! $this->auth->guard('web')->attempt($credentials)) {
            throw new \Illuminate\Auth\AuthenticationException('Invalid credentials');
        }

        $user = $this->auth->guard('web')->user();
        
        // Check if user model has createToken method (Sanctum trait)
        if (! method_exists($user, 'createToken')) {
            throw new \RuntimeException('Laravel Sanctum is not installed or User model is missing HasApiTokens trait. Please install it via composer: composer require laravel/sanctum and add use HasApiTokens to your User model');
        }
        
        // Get token abilities from options
        $abilities = $options['abilities'] ?? ['*'];
        
        // Create token
        $token = $user->createToken(
            $options['device_name'] ?? $this->request->userAgent() ?? 'auth-fusion',
            $abilities,
            $options['expires_at'] ?? null
        );

        return [
            'token' => $token->plainTextToken,
            'user' => $user,
        ];
    }

    /**
     * Logout the authenticated user.
     *
     * @param mixed $token
     * @return bool
     */
    public function logout($token): bool
    {
        $user = $this->request->user();

        if (! $user) {
            return false;
        }

        // Check if user model has tokens method (Sanctum trait)
        if (! method_exists($user, 'tokens')) {
            return false;
        }

        // If token string is provided, delete specific token
        if (is_string($token)) {
            $user->tokens()->where('token', hash('sha256', $token))->delete();
        } else {
            // Delete all tokens for current user
            $user->tokens()->delete();
        }

        return true;
    }

    /**
     * Refresh an access token.
     *
     * @param mixed $token
     * @return array ['token' => string]
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function refresh($token): array
    {
        // Sanctum doesn't have built-in refresh, so we create a new token
        $user = $this->getUser($token);
        
        if (! $user) {
            throw new \Illuminate\Auth\AuthenticationException('Invalid token');
        }

        // Delete old token
        $this->logout($token);

        // Create new token
        return $this->login([], ['device_name' => 'refreshed-token']);
    }

    /**
     * Validate a token.
     *
     * @param mixed $token
     * @return bool
     */
    public function validate($token): bool
    {
        return $this->getUser($token) !== null;
    }

    /**
     * Get user from token.
     *
     * @param mixed $token
     * @return Authenticatable|null
     */
    public function getUser($token): ?Authenticatable
    {
        // Try to find token using Sanctum's PersonalAccessToken
        try {
            if (! class_exists(\Laravel\Sanctum\PersonalAccessToken::class)) {
                return null;
            }
            
            $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
            
            return $accessToken ? $accessToken->tokenable : null;
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Get the driver name.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'sanctum';
    }
}

