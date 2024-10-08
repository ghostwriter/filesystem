<?php

declare(strict_types=1);

namespace Tests\Unit;

use Ghostwriter\Filesystem\Filesystem;
use Override;
use PHPUnit\Framework\TestCase;
use Throwable;

use const DIRECTORY_SEPARATOR;

abstract class AbstractTestCase extends TestCase
{
    protected static string $temporaryDirectory;

    /**
     * @throws Throwable
     */
    #[Override]
    protected function setUp(): void
    {
        parent::setUp();

        self::$temporaryDirectory = self::temporaryDirectory();

        self::assertDirectoryExists(self::$temporaryDirectory);
    }

    /**
     * @throws Throwable
     */
    #[Override]
    protected function tearDown(): void
    {
        Filesystem::new()->delete(self::$temporaryDirectory);

        self::assertDirectoryDoesNotExist(self::$temporaryDirectory);

        parent::tearDown();
    }

    /**
     * @throws Throwable
     */
    public static function temporaryDirectory(): string
    {
        $path = \sprintf(
            '%s%sFixture%s%s%s',
            \dirname(__DIR__),
            DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR,
            \mb_substr(\mb_strrchr(static::class, '\\'), 1),
            DIRECTORY_SEPARATOR
        );

        if (! \is_dir($path)) {
            Filesystem::new()->createDirectory($path);
        }

        return $path;
    }
}
