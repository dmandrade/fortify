<?php

namespace Laravel\Fortify\Actions;

use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;
use Laravel\Fortify\Events\TwoFactorAuthenticationEnabled;

class EnableTwoFactorAuthentication
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
        if (empty($user->two_factor_secret) || ! $this->provider->verify(decrypt($user->two_factor_secret), $code)) {
            return false;
        }

        $user->forceFill([
            'two_factor_confirmed' => true,
        ])->save();

        TwoFactorAuthenticationEnabled::dispatch($user);

        return true;
    }
}
