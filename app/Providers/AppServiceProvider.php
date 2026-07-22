<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Domain models live in domain/Models/{Model}, not App\Models — resolve
        // their factories by class basename instead of the default namespace guess.
        Factory::guessFactoryNamesUsing(function (string $modelName) {
            return 'Database\\Factories\\'.Str::afterLast($modelName, '\\').'Factory';
        });
    }
}
