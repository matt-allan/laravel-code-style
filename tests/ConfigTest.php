<?php

declare(strict_types=1);

namespace MattAllan\LaravelCodeStyle;

use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    public function test_can_use_laravel_presets()
    {
        $config = (new Config())
            ->setRules([
                '@Laravel' => true,
                '@Laravel:risky' => true,
            ])
            ->setRiskyAllowed(true);

        $this->assertArrayNotHasKey('@Laravel', $config->getRules());
        $this->assertArrayNotHasKey('@Laravel:risky', $config->getRules());

        $this->assertArrayHasKey('ordered_imports', $config->getRules());
        $this->assertSame(['sort_algorithm' => 'length'], $config->getRules()['ordered_imports']);

        $this->assertArrayHasKey('no_alias_functions', $config->getRules());
        $this->assertTrue($config->getRules()['no_alias_functions']);
    }

    public function test_can_override_preset_rules()
    {
        $config = (new Config())
            ->setRules([
                '@Laravel' => true,
                'visibility_required' => false,
            ]);

        $this->assertFalse($config->getRules()['visibility_required']);
    }
}
