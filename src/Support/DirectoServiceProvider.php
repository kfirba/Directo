<?php

namespace Kfirba\Directo\Support;

use Kfirba\Directo\Directo;
use Illuminate\Support\ServiceProvider;

class DirectoServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/directo.php' => config_path('directo.php'),
        ], 'directo');

        $this->app->singleton('directo', function () {
            $s3 = config('filesystems.disks.s3');

            return new Directo(
                $s3['bucket'],
                $s3['region'],
                $s3['key'],
                $s3['secret'],
                config('directo')
            );
        });

        $this->app->alias('directo', Directo::class);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
