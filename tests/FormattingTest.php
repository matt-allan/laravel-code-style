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
        chdir(__DIR__.'/../');

        $application = new Application();
        $application->setAutoExit(false);

        $input = new ArrayInput([
           'command' => 'fix',
           'path' => [__DIR__.'/../vendor/laravel/framework'],
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
        $ignored = [
            // violates simplified_null_return
            'vendor/laravel/framework/src/Illuminate/Http/Resources/ConditionallyLoadsAttributes.php',
            // violates no_useless_return
            'vendor/laravel/framework/src/Illuminate/Queue/DatabaseQueue.php',
        ];

        $this->assertEmpty(
            array_diff($files, $ignored),
            'Existing Laravel files should not need to be fixed.'
        );
    }
}
