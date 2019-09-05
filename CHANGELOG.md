# Changelog

All notable changes will be documented in this file.

Updates should follow the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

## 0.4.0

### Added

- Added the no_extra_blank_lines rule for tokens throw, use, and use_trait. This corresponds to StyleCI's no_blank_lines_after_throw, no_blank_lines_between_imports, and no_blank_lines_between_traits
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
