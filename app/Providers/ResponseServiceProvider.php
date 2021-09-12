<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\ResponseFactory;

class ResponseServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(ResponseFactory $factory)
    {
        $factory->macro('success', function ($statusCode = '', $data = null) use ($factory) {
            $format = [
                'success' => true,
                'code' => $statusCode,
                'results' => $data,
            ];

            return $factory->make($format);
        });

        $factory->macro('error', function (string $statusCode = '', $errors = []) use ($factory){
            $format = [
                'success' => false, 
                'code' => $statusCode,
                'error' => $errors,
            ];

            return $factory->make($format);
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}