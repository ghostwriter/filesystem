<?php

declare(strict_types=1);

namespace Ghostwriter\Filesystem\Container;

use Ghostwriter\Container\Interface\BuilderInterface;
use Ghostwriter\Container\Service\Provider\AbstractProvider;
use Ghostwriter\Filesystem\Filesystem;
use Ghostwriter\Filesystem\Interface\FilesystemInterface;
use Override;
use Throwable;

/**
 * @see FilesystemProviderTest
 */
final class FilesystemProvider extends AbstractProvider
{
    /** @throws Throwable */
    #[Override]
    public function register(BuilderInterface $builder): void
    {
        $builder->alias(FilesystemInterface::class, Filesystem::class);
    }
}
