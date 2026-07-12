<?php

declare(strict_types=1);

namespace Ghostwriter\Filesystem\Container;

use Ghostwriter\Container\Service\Provider\AbstractProvider;
use Ghostwriter\Filesystem\Filesystem;
use Ghostwriter\Filesystem\Interface\FilesystemInterface;

/**
 * @see FilesystemProviderTest
 */
final class FilesystemProvider extends AbstractProvider
{
    /**
     * [alias => service].
     *
     * @var array<class-string,class-string>
     */
    public const array ALIAS = [
        FilesystemInterface::class => Filesystem::class,
    ];
}
