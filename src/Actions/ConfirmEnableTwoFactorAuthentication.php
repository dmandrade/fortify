<?php

namespace Laravel\Fortify\Actions;

use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;

class ConfirmEnableTwoFactorAuthentication
{
    /**
     * The two factor authentication provider.
     *
     * @var \Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider
     */
    protected $provider;

    /**
     * Create a new action instance.
     *
     * @param  \Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider  $provider
     * @return void
     */
    public function __construct(TwoFactorAuthenticationProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Enable two factor authentication for the user.
     *
     * @param  \Illuminate\Foundation\Auth\User  $user
     * @param  string  $code
     * @return bool
     */
    public function __invoke($user, $code)
    {
        if (! $this->provider->verify(decrypt($user->two_factor_secret), $code)) {
            return false;
        }

        $user->two_factor_confirmed = true;
        $user->save();

        return true;
    }
}
