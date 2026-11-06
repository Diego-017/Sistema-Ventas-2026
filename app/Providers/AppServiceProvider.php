<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ViewErrorBag;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Asegurar que $errors siempre esté disponible en todas las vistas
        View::composer('*', function ($view) {
            if (!isset($view->errors)) {
                $view->with('errors', session('errors', new ViewErrorBag));
            }
        });
    }
}
