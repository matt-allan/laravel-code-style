<?php

require __DIR__ . '/../../vendor/autoload.php';

return (new MattAllan\LaravelCodeStyle\Config())
    ->setRules([
        '@Laravel' => true,
    ]);
