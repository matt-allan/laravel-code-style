# Changelog

All notable changes will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## Unreleased

### Added
### Changed 
### Removed 

## 0.7.1

- Added deprecation notice to composer.json.

## 0.7.0

### Added

- Added the `array_indentation`, `clean_namespace`, `no_alias_language_construct_call`, `lambda_not_used_import`, `switch_continue_to_break`, `phpdoc_inline_tag_normalizer`, `integer_literal_case`, `no_space_around_double_colon`, and `types_spaces` rules.
- Added support for PHP 8.0.
- Added support for PHP-CS-Fixer 3.x

### Changed

- The `method_argument_space` rule was updated to ignore multiline method calls.

### Removed

- Removed the `phpdoc_inline_tag` rule.
- Removed the `braces` rule.
- Dropped PHP 7.2 and PHP 7.3 support.
- Dropped support for `illuminate/support` `5.7.x|5.8.x|^6.0`. You will need to use `^7.0` going forward.
- Removed support for PHP-CS-Fixer versions before 3.2.0.

## 0.6.0

### Added

- Added support for Laravel 8.

### Changed

- Switched the `concat_space` rule from `true` to `['spacing' => 'none']`. Functionally it's the same, it's just more readable.
- Changed the default seeders path from `seeds` to `seeders` in the default `.php_cs` config file to match Laravel 8.0. If you haven't edited your `.php_cs` you can pull in the updated version by running `php artisan vendor:publish --provider="MattAllan\LaravelCodeStyle\ServiceProvider" --force`.

### Removed

### Security

## 0.5.1

### Added

- Added support for Laravel 7.

## 0.5.0

### Added

- Added the `list_syntax` rule.
- Switched from length sorted imports to alpha sorted imports.

## 0.4.0

### Added

- Added the `no_extra_blank_lines` rule for tokens `throw`, `use`, and `use_trait`. This corresponds to StyleCI's `no_blank_lines_after_throw`, `no_blank_lines_between_imports`, and `no_blank_lines_between_traits`
rules.
- Added support for Laravel 6.0.

### Deprecated

### Fixed

- Explicitly format the database seeds and factories folders rather than formatting the entire database path and excluding the migrations folder. This change was necessary to prevent migrations from being modified by the fixer. Since this change only affects the default config you will either need to republish the config or manually apply the changes yourself.

### Removed

- Dropped PHP 7.1 support.

### Security

## 0.3.0

### Added

- Added support for installation in 5.7.* Laravel apps
