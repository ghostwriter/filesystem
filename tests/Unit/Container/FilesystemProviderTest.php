<?php

declare(strict_types=1);

namespace Tests\Unit\Container;

use Ghostwriter\Container\Container;
use Ghostwriter\Container\Interface\BuilderInterface;
use Ghostwriter\Container\Interface\Service\ProviderInterface;
use Ghostwriter\Container\Service\Provider\AbstractProvider;
use Ghostwriter\Filesystem\Container\FilesystemProvider;
use Ghostwriter\Filesystem\Filesystem;
use Ghostwriter\Filesystem\Interface\FilesystemInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\UsesClassesThatImplementInterface;
use Tests\Unit\AbstractTestCase;

#[CoversClass(FilesystemProvider::class)]
#[UsesClass(Filesystem::class)]
#[UsesClassesThatImplementInterface(FilesystemInterface::class)]
final class FilesystemProviderTest extends AbstractTestCase
{
    public function testExtendsAbstractProvider(): void
    {
        self::assertInstanceOf(AbstractProvider::class, new FilesystemProvider());
    }

    public function testFilesystemCanBeInstantiatedFromContainer(): void
    {
        $container = Container::getInstance();
        self::assertInstanceOf(Filesystem::class, $container->get(FilesystemInterface::class));
    }

    public function testFilesystemProviderRegister(): void
    {
        $filesystemProvider = new FilesystemProvider();

        $container = $this->createMock(BuilderInterface::class);

        $container->expects(self::once())
            ->method('alias')
            ->with(FilesystemInterface::class, Filesystem::class);

        $container->expects(self::never())->method('bind');
        $container->expects(self::never())->method('extend');
        $container->expects(self::never())->method('factory');
        $container->expects(self::never())->method('set');
        $container->expects(self::never())->method('unset')->seal();

        $filesystemProvider->register($container);
    }

    public function testImplementsProviderInterface(): void
    {
        self::assertInstanceOf(ProviderInterface::class, new FilesystemProvider());
    }
}
