<?php

namespace Karim007\LaravelBkashTokenize;

use Karim007\LaravelBkashTokenize\Payment\TBPayment;
use Illuminate\Support\ServiceProvider;
use Karim007\LaravelBkashTokenize\Payment\TBRefund;

class BkashTokenizeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . "/../config/bkash.php" => config_path("bkash.php")
        ],'config');
        $this->publishes([
            __DIR__.'/Controllers/BkashTokenizePaymentController.php' => app_path('Http/Controllers/BkashTokenizePaymentController.php'),
        ],'controllers');

        $this->loadRoutesFrom(__DIR__ . "/routes/bkash_route.php");
        $this->loadViewsFrom(__DIR__ . '/Views', 'bkashT');
    }

    /**
     * Register application services
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . "/../config/bkash.php", "bkash");

        $this->app->bind("tbpayment", function () {
            return new TBPayment();
        });
        $this->app->bind("tbrefund", function () {
            return new TBRefund();
        });
    }
}
