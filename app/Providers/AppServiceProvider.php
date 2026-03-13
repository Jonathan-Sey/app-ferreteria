<?php 

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use BladeUI\Icons\Factory;
use Illuminate\Contracts\Container\Container;

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
        
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['es', 'en'])
                ->labels([
                    'es' => 'Español',
                    'en' => 'Inglés',
                ]);
        });

        $this->callAfterResolving(Factory::class, function (Factory $factory, Container $container) {
            $factory->add('custom-icons', [
                'path' => resource_path('views/icons'),
                'prefix' => 'custom',
            ]);
        });

    }
}