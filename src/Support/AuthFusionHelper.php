<?php

namespace Obrainwave\AuthFusion\Support;

use Obrainwave\AuthFusion\Facades\AuthFusion;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class AuthFusionHelper
{
    /**
     * Get current authenticated user from request.
     *
     * @param Request|null $request
     * @return Authenticatable|null
     */
    public static function user(?Request $request = null): ?Authenticatable
    {
        $request = $request ?? request();
        $token = $request->bearerToken();

        if (!$token) {
            return null;
        }

        return AuthFusion::driver()->getUser($token);
    }

    /**
     * Check if user is authenticated.
     *
     * @param Request|null $request
     * @return bool
     */
    public static function check(?Request $request = null): bool
    {
        return self::user($request) !== null;
    }

    /**
     * Get token from request.
     *
     * @param Request|null $request
     * @return string|null
     */
    public static function token(?Request $request = null): ?string
    {
        $request = $request ?? request();
        return $request->bearerToken();
    }

    /**
     * Validate token.
     *
     * @param string|null $token
     * @return bool
     */
    public static function validate(?string $token = null): bool
    {
        $token = $token ?? self::token();

        if (!$token) {
            return false;
        }

        return AuthFusion::driver()->validate($token);
    }

    /**
     * Get all available drivers.
     *
     * @return array
     */
    public static function availableDrivers(): array
    {
        return ['sanctum', 'jwt', 'passport'];
    }
}

