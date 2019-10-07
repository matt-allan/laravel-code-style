<?php

declare(strict_types=1);

namespace MattAllan\LaravelCodeStyle;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{
    public const VERSION5 = 5;
    public const VERSION6 = 6;

    public function boot()
    {
        $version = (int) $this->app->version();

        $from = __DIR__.'/../config/.php_cs';

        if (in_array($version, [self::VERSION5, self::VERSION6], true)) {
            $from .= $version;
        }

        $this->publishes([
            $from => base_path('.php_cs'),
        ], 'config');
    }
}
