<?php

declare(strict_types=1);

namespace MattAllan\LaravelCodeStyle;

use Illuminate\Filesystem\Filesystem;
use Mockery;
use Orchestra\Testbench\TestCase;

class ServiceProviderTest extends TestCase
{
    public function test_service_provider_boots(): void
    {
        $mockFileSystem = $this->mock(Filesystem::class);
        $this->instance('files', $mockFileSystem);

        $mockFileSystem
            ->shouldReceive('isFile')
            ->with(Mockery::on(static function ($a) {
                return strpos($a, 'config/.php-cs-fixer.dist.php') > 0;
            }))
            ->andReturn(true)
            ->once();
        $mockFileSystem->shouldReceive('exists')->andReturn(false)->once();
        $mockFileSystem->shouldReceive('isDirectory')->andReturn(true)->once();
        $mockFileSystem->shouldReceive('copy')->once();

        $this->artisan('vendor:publish', [
            '--provider' => ServiceProvider::class,
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class,
        ];
    }
}
