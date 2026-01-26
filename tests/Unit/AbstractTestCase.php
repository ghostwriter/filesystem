<?php

declare(strict_types=1);

namespace Tests\Unit;

use Ghostwriter\Filesystem\Filesystem;
use Ghostwriter\Filesystem\Interface\FilesystemInterface;
use Override;
use PHPUnit\Framework\TestCase;
use Throwable;

use const DIRECTORY_SEPARATOR;

use function getenv;
use function implode;
use function mb_strrchr;
use function mb_substr;
use function realpath;
use function sys_get_temp_dir;

abstract class AbstractTestCase extends TestCase
{
    public readonly FilesystemInterface $filesystem;

    protected static string $temporaryDirectory;

    /** @throws Throwable */
    #[Override]
    protected function setUp(): void
    {
        $this->filesystem = Filesystem::new();

        self::$temporaryDirectory = $this->temporaryDirectory();

        if ($this->filesystem->missing(self::$temporaryDirectory)) {
            $this->filesystem->createDirectory(self::$temporaryDirectory);
        }

        $this->filesystem->chmod(self::$temporaryDirectory, 0o777);

        parent::setUp();
    }

    /** @throws Throwable */
    #[Override]
    protected function tearDown(): void
    {
        if ($this->filesystem->isDirectory(self::$temporaryDirectory)) {
            $this->filesystem->deleteDirectory(self::$temporaryDirectory);
        }

        self::assertDirectoryDoesNotExist(self::$temporaryDirectory);

        parent::tearDown();
    }

    private function storage(): string
    {
        static $storage = null;

        return $storage ??= realpath(getenv('RUNNER_TEMP') ?: sys_get_temp_dir());
    }

    private function temporaryDirectory(): string
    {
        static $directories = [];

        $name = mb_substr(mb_strrchr(static::class, '\\'), 1);

        return $directories[$name] ??=
            implode(DIRECTORY_SEPARATOR, [$this->storage(), $name]) . DIRECTORY_SEPARATOR;
    }
}
