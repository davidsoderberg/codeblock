<?php namespace App\Providers;

use Illuminate\Support\Facades\View;
use Validator;
use Illuminate\Support\ServiceProvider;

/**
 * Class AppServiceProvider
 * @package App\Providers
 */
class AppServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('honeypot', function ($attribute, $value, $parameters) {
            return $value == '';
        });

        View::composer('*', function ($view) {
            View::share('view_name', $view->getName());
        });
    }

    /**
     * Register any application services.
     *
     * This service provider is a great spot to register your various container
     * bindings with the application. As you can see, we are registering our
     * "Registrar" implementation here. You can add your own bindings too!
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            'Illuminate\Contracts\Auth\Registrar',
            'App\Services\Registrar'
        );

        \Blade::setRawTags('{{', '}}');
        \Blade::setContentTags('{{{', '}}}');
        \Blade::setEscapedContentTags('{{{', '}}}');
    }
}
