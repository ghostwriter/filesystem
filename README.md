# Filesystem

[![Automation](https://github.com/ghostwriter/filesystem/actions/workflows/automation.yml/badge.svg)](https://github.com/ghostwriter/filesystem/actions/workflows/automation.yml)
[![Supported PHP Version](https://badgen.net/packagist/php/ghostwriter/filesystem?color=8892bf)](https://www.php.net/supported-versions)
[![GitHub Sponsors](https://img.shields.io/github/sponsors/ghostwriter?label=Sponsor+@ghostwriter/filesystem&logo=GitHub+Sponsors)](https://github.com/sponsors/ghostwriter)
[![Code Coverage](https://codecov.io/gh/ghostwriter/filesystem/branch/main/graph/badge.svg)](https://codecov.io/gh/ghostwriter/filesystem)
[![Type Coverage](https://shepherd.dev/github/ghostwriter/filesystem/coverage.svg)](https://shepherd.dev/github/ghostwriter/filesystem)
[![Psalm Level](https://shepherd.dev/github/ghostwriter/filesystem/level.svg)](https://psalm.dev/docs/running_psalm/error_levels)
[![Latest Version on Packagist](https://badgen.net/packagist/v/ghostwriter/filesystem)](https://packagist.org/packages/ghostwriter/filesystem)
[![Downloads](https://badgen.net/packagist/dt/ghostwriter/filesystem?color=blue)](https://packagist.org/packages/ghostwriter/filesystem)

Filesystem implementation for PHP

> [!WARNING]
>
> This project is not finished yet, work in progress.

## Installation

You can install the package via composer:

``` bash
composer require ghostwriter/filesystem
```

### Star ⭐️ this repo if you find it useful

You can also star (🌟) this repo to find it easier later.

## Usage

```php
use GhostWriter\Filesystem\Filesystem;

$filesystem = new Filesystem();

$currentDirectory = $filesystem->currentWorkingDirectory();

$filesystem->write($currentDirectory . '/blm.txt', '#BlackLivesMatter');

$content = $filesystem->read($currentDirectory . '/blm.txt');

echo $content; // #BlackLivesMatter
```

### Credits

- [Nathanael Esayeas](https://github.com/ghostwriter)
- [All Contributors](https://github.com/ghostwriter/filesystem/contributors)

### Changelog

Please see [CHANGELOG.md](./CHANGELOG.md) for more information on what has changed recently.

### License

Please see [LICENSE](./LICENSE) for more information on the license that applies to this project.

### Security

Please see [SECURITY.md](./SECURITY.md) for more information on security disclosure process.
