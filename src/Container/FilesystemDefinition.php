<?php

declare(strict_types=1);

namespace Ghostwriter\Filesystem\Container;

use Ghostwriter\Container\Interface\ContainerInterface;
use Ghostwriter\Container\Interface\Service\DefinitionInterface;
use Ghostwriter\Filesystem\Filesystem;
use Ghostwriter\Filesystem\Interface\FilesystemInterface;
use Override;
use Throwable;

/**
 * @see FilesystemDefinitionTest
 */
final readonly class FilesystemDefinition implements DefinitionInterface
{
    /** @throws Throwable */
    #[Override]
    public function __invoke(ContainerInterface $container): void
    {
        $container->alias(Filesystem::class, FilesystemInterface::class);
    }
}
