## Laravel Code Style
[![Packagist License](https://poser.pugx.org/matt-allan/laravel-code-style/license.png)](http://choosealicense.com/licenses/mit/)
[![Latest Stable Version](https://poser.pugx.org/matt-allan/laravel-code-style/version.png)](https://packagist.org/packages/matt-allan/laravel-code-style)
[![Build Status](https://secure.travis-ci.org/matt-allan/laravel-code-style.png?branch=master)](https://travis-ci.org/matt-allan/laravel-code-style)


This package provides automatic code style checking and formatting for Laravel applications and packages.

The package adds the [php-cs-fixer](https://github.com/FriendsOfPhp/PHP-CS-Fixer) tool and a community maintained ruleset to your application.  The ruleset is a best effort attempt to match the code style the Laravel framework itself uses.  Check out an [example](./examples/User.php) to see what the code style looks like.

You might want to use this package if you are writing a Laravel application, package or tutorial and you want to match the framework's code style.

![example code formatting](https://repository-images.githubusercontent.com/182856423/6d64dc80-6526-11e9-835c-d54082bd2196)

## Installation

Require this package with composer. It is recommended to only require the package for development.

```shell
composer require matt-allan/laravel-code-style --dev
```

The service provider will be automatically registered using [package discovery](https://laravel.com/docs/5.8/packages#package-discovery).

If you don't use auto-discovery you should add the service provider to the providers array in `config/app.php`.

```php
// existing providers...
MattAllan\LaravelCodeStyle\ServiceProvider::class,
```

Once the package is installed you should publish the configuration.

```shell
php artisan vendor:publish --provider="MattAllan\LaravelCodeStyle\ServiceProvider"
```

Publishing the config will add a `.php_cs` configuration file to the root of your project.  You may customize this file as needed.  The `.php_cs` file should be committed to version control.

A cache file will be written to `.php_cs.cache` in the project root the first time you run the fixer.  You should ignore this file so it is not added to your version control system.

```shell
echo '.php_cs.cache' >> .gitignore
```

## Usage

Once the package is installed you can check and fix your code formatting with the `php-cs-fixer` command.  The command will be available in Composer's `vendor/bin` directory.

### Fixing

To automatically fix the code style of your project you may use the `php-cs-fixer fix` command.

```shell
vendor/bin/php-cs-fixer fix
```

This will automatically fix the code style of every file in your project.

By default only the file names of every file fixed will be shown.  To see a full diff of every change append the `--diff` flag.

```shell
vendor/bin/php-cs-fixer fix --diff
```

### Checking

If you would like to check the formatting without actually altering any files you should use the `fix` command with the `--dry-run` flag.

```shell
vendor/bin/php-cs-fixer fix --dry-run --diff
```

In dry-run mode any violations will [cause the command to return a non-zero exit code](https://github.com/FriendsOfPhp/PHP-CS-Fixer#exit-code).  You can use this command to fail a CI build or git commit hook.

### Composer script

To make checking and fixing code style easier for contributors to your project it's recommended to add the commands as a [composer script](https://getcomposer.org/doc/articles/scripts.md).

The following example allows anyone to check the code style by calling `composer check-style` and to fix the code style with `composer fix-style`.

```javascript
{
    // ...
    "scripts": {
        "check-style": "php-cs-fixer fix --dry-run --diff",
        "fix-style": "php-cs-fixer fix"
    }
}
```

### More Options

For a complete list of options please consult the [php-cs-fixer documentation](https://github.com/FriendsOfPhp/PHP-CS-Fixer#usage).

## Configuration

The default configuration is published as `.php_cs` in the project root.  You can customize this file to change options such as the paths searched or the fixes applied.

### Paths

You can change the paths searched for PHP files by chaining method calls onto the `PhpCsFixer\Finder` instance being passed to the `MattAllan\LaravelCodeStyle\Config::setFinder` method.

For example, to search the `examples` directory you would append `->in('examples')`:

```php
<?php

require __DIR__ . '/vendor/autoload.php';

return (new MattAllan\LaravelCodeStyle\Config())
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(app_path())
            // ...
            ->in('examples')
    )
    // ...

```

The default paths are setup for a Laravel application.  If you are writing a package the path helper functions will not available and you will need to change the paths as necessary, i.e. `PhpCsFixer\Finder::create()->in(__DIR__)`.

For a complete list of options refer to the [Symfony Finder documentation](https://symfony.com/doc/current/components/finder.html).

### Rules

By default only the `@Laravel` preset is enabled.  This preset enforces the [PSR-2 standard](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md) as well as nearly 100 other rules such as ordering use statements by length and requiring trailing commas in multiline arrays.

A `@Laravel:risky` preset is also available.  The `@Laravel:risky` preset enables rules that may change code behavior.  To enable risky rules you need to add the preset and set `isRiskyEnabled` to true.

```php
return (new MattAllan\LaravelCodeStyle\Config())
        ->setFinder(
            // ...
        )
        ->setRules([
            '@Laravel' => true,
            '@Laravel:risky' => true,
        ])
        ->setRiskyAllowed(true);
```

It is possible to override a specific rule from the preset.  For example, you could disable the `no_unused_imports` rule like this:

```php
return (new MattAllan\LaravelCodeStyle\Config())
        ->setFinder(
            // ...
        )
        ->setRules([
            '@Laravel' => true,
            'no_unused_imports' => false,
        ]);
```

For a complete list of available rules please refer to the [php-cs-fixer documentation](https://github.com/fabpot/PHP-CS-Fixer).

## Continuous Integration

To automatically fix the code style when someone opens a pull request or pushes a commit check out [StyleCI](https://styleci.io).  StyleCI wrote many of the open source fixer rules this package depends on and StyleCI's Laravel preset is the official definition of Laravel's code style.

## Editor Support

Any editor plugin for php-cs-fixer will work. Check the [php-cs-fixer readme](https://github.com/fabpot/PHP-CS-Fixer#helpers) for more info.

## How It Works

Laravel does not publish an official php-cs-fixer ruleset.  To create the rule set we compare StyleCI's preset to the available php-cs-fixer rules.  In some cases StyleCI is using a rule that is no longer available.  For these rules we have to dig through the git history of php-cs-fixer and determine which rule replaced the deprecated rule.

It isn't possible to add your own presets to php-cs-fixer.  Instead `PhpCsFixer\Config` is extended to search the rules for our custom presets and merge the rules if they are found.

To ensure the rules stay in sync an automated test formats the entire Laravel framework and compares the results.  If an existing Laravel file does not match our rule set the build is failed.

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](./github/CONTRIBUTING.md) for details.

## Credits

- [Matt Allan](https://github.com/matt-allan)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
