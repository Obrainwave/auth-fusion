<?php

namespace Obrainwave\AuthFusion\Drivers;

use Obrainwave\AuthFusion\Contracts\AuthDriverInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Factory as AuthFactory;

class JWTDriver implements AuthDriverInterface
{
    /**
     * @var AuthFactory
     */
    protected $auth;

    /**
     * Create a new JWTDriver instance.
     *
     * @param AuthFactory $auth
     */
    public function __construct(AuthFactory $auth)
    {
        $this->auth = $auth;
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
        try {
            // Use JWTAuth facade for attempt method
            if (!class_exists(\Tymon\JWTAuth\Facades\JWTAuth::class)) {
                throw new \RuntimeException('tymon/jwt-auth is not installed. Please install it via composer: composer require tymon/jwt-auth');
            }
            
            $token = \Tymon\JWTAuth\Facades\JWTAuth::attempt($credentials);

            if (! $token) {
                throw new \Illuminate\Auth\AuthenticationException('Invalid credentials');
            }

            $user = \Tymon\JWTAuth\Facades\JWTAuth::user();

            return [
                'token' => $token,
                'user' => $user,
            ];
        } catch (\BadMethodCallException $e) {
            throw new \RuntimeException($e->getMessage());
        } catch (\Exception $e) {
            throw new \RuntimeException('JWT authentication error: ' . $e->getMessage());
        }
    }

    /**
     * Logout the authenticated user.
     *
     * @param mixed $token
     * @return bool
     */
    public function logout($token): bool
    {
        try {
            if (!class_exists(\Tymon\JWTAuth\Facades\JWTAuth::class)) {
                return false;
            }
            
            \Tymon\JWTAuth\Facades\JWTAuth::setToken($token)->invalidate();
            return true;
        } catch (\Exception $e) {
            return false;
        }
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
        try {
            if (!class_exists(\Tymon\JWTAuth\Facades\JWTAuth::class)) {
                throw new \RuntimeException('tymon/jwt-auth is not installed. Please install it via composer: composer require tymon/jwt-auth');
            }
            
            $newToken = \Tymon\JWTAuth\Facades\JWTAuth::setToken($token)->refresh();
            
            return [
                'token' => $newToken,
            ];
        } catch (\BadMethodCallException $e) {
            throw new \RuntimeException('tymon/jwt-auth is not installed. Please install it via composer: composer require tymon/jwt-auth');
        } catch (\Exception $e) {
            throw new \Illuminate\Auth\AuthenticationException('Failed to refresh token');
        }
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
        try {
            if (!class_exists(\Tymon\JWTAuth\Facades\JWTAuth::class)) {
                return null;
            }
            
            return \Tymon\JWTAuth\Facades\JWTAuth::setToken($token)->authenticate();
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
        return 'jwt';
    }

}

