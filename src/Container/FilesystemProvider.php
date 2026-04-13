<?php

declare(strict_types=1);

namespace Ghostwriter\Filesystem\Container;

use Ghostwriter\Container\Interface\BuilderInterface;
use Ghostwriter\Container\Interface\ContainerInterface;
use Ghostwriter\Container\Interface\Service\ProviderInterface;
use Ghostwriter\Filesystem\Filesystem;
use Ghostwriter\Filesystem\Interface\FilesystemInterface;
use Override;
use Throwable;

/**
 * @see FilesystemProviderTest
 */
final readonly class FilesystemProvider implements ProviderInterface
{
    /** @throws Throwable */
    #[Override]
    public function boot(ContainerInterface $container): void {}

    /** @throws Throwable */
    #[Override]
    public function register(BuilderInterface $builder): void
    {
        $builder->alias(FilesystemInterface::class, Filesystem::class);
    }
}
