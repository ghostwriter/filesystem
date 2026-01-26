<?php

declare(strict_types=1);

namespace Tests\Unit\Container;

use Ghostwriter\Container\Container;
use Ghostwriter\Filesystem\Container\FilesystemDefinition;
use Ghostwriter\Filesystem\Filesystem;
use Ghostwriter\Filesystem\Interface\FilesystemInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\Unit\AbstractTestCase;

#[CoversClass(Filesystem::class)]
#[CoversClass(FilesystemDefinition::class)]
final class FilesystemDefinitionTest extends AbstractTestCase
{
    public function testFilesystemCanBeInstantiatedFromContainer(): void
    {
        $container = Container::getInstance();

        $container->define(FilesystemDefinition::class);

        $filesystem = $container->get(FilesystemInterface::class);

        self::assertInstanceOf(Filesystem::class, $filesystem);
    }
}
