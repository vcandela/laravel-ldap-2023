<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot()
    {
        $this->registerPolicies();

        Fortify::authenticateUsing(function ($request) {
            Log::alert($request);

            // Validar si el usuario existe en la tabla "users"
            $user = User::where('email', $request->username)->first();
            if ($user) {
                // Autenticar al usuario con la tabla "users"
                if (Hash::check($request->password, $user->password)) {
                    return $user;
                }
            }

            $validated = Auth::validate([
                'cn' => $request->username,
                'password' => $request->password
            ]);

            $result =  $validated ? Auth::getLastAttempted() : null;
            Log::alert($result);
            return $result;

        });
    }
}
