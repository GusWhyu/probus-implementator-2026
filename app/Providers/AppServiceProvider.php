<?php

namespace App\Providers;

use App\Models\User;
use Gate;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useTailwind();

        Gate::define('admin', function(User $user){
            return $user->usertype === 'admin';
        });

        Gate::define('aspv', function(User $user){
            return $user->usertype !== 'user';
        });
    }
}
