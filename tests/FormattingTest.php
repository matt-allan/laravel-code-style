<?php

declare(strict_types=1);

namespace MattAllan\LaravelCodeStyle;

use PHPUnit\Framework\TestCase;
use PhpCsFixer\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

class FormattingTest extends TestCase
{
    public function test_formatting_matches_laravel()
    {
        $application = new Application();
        $application->setAutoExit(false);

        $input = new ArrayInput([
           'command' => 'fix',
           'path' => [__DIR__.'/../vendor/laravel/framework'],
           '--config' => __DIR__ . '/fixtures/.php_cs',
           '--dry-run' => true,
           '--format' => 'json',
        ]);

        $output = new BufferedOutput();
        $application->run($input, $output);

        $content = json_decode($output->fetch(), true);

        $files = array_map(function (array $file) {
            return $file['name'];
        }, $content['files']);

        // These files violate the rules Laravel uses, I guess they need to be fixed.
        // to debug: vendor/bin/php-cs-fixer fix --dry-run --config=tests/fixtures/.php_cs ./vendor/laravel/framework/ --format checkstyle
        $ignored = [
            'vendor/laravel/framework/src/Illuminate/Database/Query/Builder.php',
            'vendor/laravel/framework/src/Illuminate/Pagination/UrlWindow.php',
            'vendor/laravel/framework/src/Illuminate/Broadcasting/Broadcasters/Broadcaster.php',
            'vendor/laravel/framework/src/Illuminate/Cache/NullStore.php',
            'vendor/laravel/framework/src/Illuminate/Cache/DynamoDbStore.php',
            'vendor/laravel/framework/src/Illuminate/Cache/ArrayStore.php',
            'vendor/laravel/framework/src/Illuminate/Auth/EloquentUserProvider.php',
            'vendor/laravel/framework/src/Illuminate/Auth/Access/Gate.php',
            'vendor/laravel/framework/src/Illuminate/Foundation/Application.php',
            'vendor/laravel/framework/src/Illuminate/Foundation/Testing/Concerns/InteractsWithExceptionHandling.php',
            'vendor/laravel/framework/src/Illuminate/Foundation/Console/RouteListCommand.php',
            'vendor/laravel/framework/src/Illuminate/Support/Str.php',
            'vendor/laravel/framework/src/Illuminate/Http/Resources/ConditionallyLoadsAttributes.php',
            'vendor/laravel/framework/src/Illuminate/Queue/DatabaseQueue.php',
        ];

        $this->assertEmpty(
            array_diff($files, $ignored),
            'Existing Laravel files should not need to be fixed.'
        );
    }
}
