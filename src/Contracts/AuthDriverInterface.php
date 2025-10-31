<?php

namespace Obrainwave\AuthFusion\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface AuthDriverInterface
{
    /**
     * Authenticate user with credentials.
     *
     * @param array $credentials
     * @param array $options
     * @return array ['token' => string, 'user' => Authenticatable]
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function login(array $credentials, array $options = []): array;

    /**
     * Logout the authenticated user.
     *
     * @param mixed $token
     * @return bool
     */
    public function logout($token): bool;

    /**
     * Refresh an access token.
     *
     * @param mixed $token
     * @return array ['token' => string]
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function refresh($token): array;

    /**
     * Validate a token.
     *
     * @param mixed $token
     * @return bool
     */
    public function validate($token): bool;

    /**
     * Get user from token.
     *
     * @param mixed $token
     * @return Authenticatable|null
     */
    public function getUser($token): ?Authenticatable;

    /**
     * Get the driver name.
     *
     * @return string
     */
    public function getName(): string;
}

