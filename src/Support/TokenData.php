<?php

namespace Obrainwave\AuthFusion\Support;

class TokenData
{
    /**
     * @var string
     */
    public $token;

    /**
     * @var \Illuminate\Contracts\Auth\Authenticatable
     */
    public $user;

    /**
     * @var array
     */
    public $extra;

    /**
     * Create a new TokenData instance.
     *
     * @param string $token
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @param array $extra
     */
    public function __construct(string $token, $user, array $extra = [])
    {
        $this->token = $token;
        $this->user = $user;
        $this->extra = $extra;
    }

    /**
     * Convert to array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_merge([
            'token' => $this->token,
            'user' => $this->user,
        ], $this->extra);
    }
}

