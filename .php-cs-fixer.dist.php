<?php

require __DIR__ . '/vendor/autoload.php';

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__);

return (new MattAllan\LaravelCodeStyle\Config())
        ->setFinder($finder)
        ->setRules([
            '@Laravel' => true,
            '@Laravel:risky' => true,
        ])
        ->setRiskyAllowed(true);
