<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use Laravel\Fortify\Fortify;
use App\Http\Responses\RegisterResponse;
use App\Http\Responses\LoginResponse;
use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use App\Http\Responses\LogoutResponse;
use Laravel\Fortify\Contracts\LogoutResponse as LogoutResponseContract;


class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //会員登録後のレスポンスをカスタマイズ
    $this->app->singleton(RegisterResponseContract::class, RegisterResponse::class);

    // ログイン後のレスポンスをカスタマイズ
    $this->app->singleton(LoginResponseContract::class, LoginResponse::class);

    $this->app->singleton(LogoutResponseContract::class, LogoutResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Fortify::verifyEmailView(function(){
            return view('auth.verify-email');
        });

        Fortify::createUsersUsing(CreateNewUser::class);

        Fortify::registerView(function(){
            return view('auth.register');
        });
        Fortify::loginView(function(){
            return view('auth.login');
        });

        Fortify::verifyEmailView(function(){
            return view('auth.verify-email');
        });
        RateLimiter::for('login', function (Request $request) {
        return Limit::perMinute(10)->by($request->email.$request->ip());
        });
    }
}