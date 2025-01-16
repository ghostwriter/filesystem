<?php

declare(strict_types=1);

namespace Tests\Unit;

use Ghostwriter\Filesystem\Filesystem;
use Ghostwriter\Filesystem\Interface\FilesystemInterface;
use Override;
use PHPUnit\Framework\TestCase;
use Throwable;

use const DIRECTORY_SEPARATOR;

use function array_key_exists;
use function dirname;
use function implode;
use function mb_strrchr;
use function mb_substr;

abstract class AbstractTestCase extends TestCase
{
    public readonly FilesystemInterface $filesystem;

    protected static string $temporaryDirectory;

    /**
     * @throws Throwable
     */
    #[Override]
    protected function setUp(): void
    {
        $this->filesystem = Filesystem::new();

        self::$temporaryDirectory = $this->temporaryDirectory();

        parent::setUp();
    }

    /**
     * @throws Throwable
     */
    #[Override]
    protected function tearDown(): void
    {
        if ($this->filesystem->isDirectory(self::$temporaryDirectory)) {
            $this->filesystem->deleteDirectory(self::$temporaryDirectory);
        }

        self::assertDirectoryDoesNotExist(self::$temporaryDirectory);

        parent::tearDown();
    }

    /**
     * @throws Throwable
     */
    public function temporaryDirectory(): string
    {
        static $directories = [];

        $name = mb_substr(mb_strrchr(static::class, '\\'), 1);
        if (array_key_exists($name, $directories)) {
            return $directories[$name];
        }

        $path = implode(DIRECTORY_SEPARATOR, [dirname(__DIR__), 'fixture', $name, '']);

        if (! $this->filesystem->isDirectory($path)) {
            $this->filesystem->createDirectory($path);
        }

        self::assertDirectoryExists($path);

        return $directories[$name] = $path;
    }
}
