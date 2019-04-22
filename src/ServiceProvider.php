<?php

declare(strict_types=1);

namespace MattAllan\LaravelCodeStyle;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/.php_cs' => base_path('.php_cs'),
        ], 'config');
    }
}
