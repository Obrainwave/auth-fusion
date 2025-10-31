<?php

namespace Obrainwave\AuthFusion\Drivers;

use Obrainwave\AuthFusion\Contracts\AuthDriverInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Factory as AuthFactory;

class PassportDriver implements AuthDriverInterface
{
    /**
     * @var AuthFactory
     */
    protected $auth;

    /**
     * Create a new PassportDriver instance.
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
        // Attempt authentication
        if (! $this->auth->guard('web')->attempt($credentials)) {
            throw new \Illuminate\Auth\AuthenticationException('Invalid credentials');
        }

        $user = $this->auth->guard('web')->user();

        // Check if user model has createToken method (Passport trait)
        if (! method_exists($user, 'createToken')) {
            throw new \RuntimeException('Laravel Passport is not installed or User model is missing HasApiTokens trait. Please install it via composer: composer require laravel/passport and add use HasApiTokens to your User model');
        }

        // Create token using Passport
        $token = $user->createToken(
            $options['name'] ?? 'auth-fusion',
            $options['scopes'] ?? []
        );

        return [
            'token' => $token->accessToken,
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
        try {
            if (is_string($token)) {
                // Revoke specific token
                $tokenModel = \Laravel\Passport\Token::where('id', $token)->first();
                if ($tokenModel) {
                    $tokenModel->revoke();
                }
            } else {
                // Revoke all tokens for current user
                $user = request()->user();
                if ($user) {
                    $user->tokens->each(function ($tokenModel) {
                        $tokenModel->revoke();
                    });
                }
            }

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
        // Passport doesn't have a simple refresh mechanism
        // This would typically involve OAuth2 refresh token flow
        throw new \BadMethodCallException('Passport refresh requires OAuth2 refresh token flow implementation');
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
            $tokenModel = \Laravel\Passport\Token::find($token);
            
            if ($tokenModel && ! $tokenModel->revoked) {
                return $tokenModel->user;
            }

            return null;
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
        return 'passport';
    }
}

