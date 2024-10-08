<?php

declare(strict_types=1);

namespace Tests\Unit;

use Generator;
use Ghostwriter\Filesystem\Filesystem;
use Ghostwriter\Filesystem\Interface\PathInterface;
use Ghostwriter\Filesystem\Path;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;

#[CoversClass(Path::class)]
#[CoversClass(Filesystem::class)]
final class PathTest extends AbstractTestCase
{
    /**
     * @param non-empty-string $path
     */
    #[DataProvider('providePathCases')]
    public function testPath(string $path): void
    {
        $instance = Path::new($path);

        self::assertSame($path, $instance->toString());

        self::assertSame($path, $instance->__toString());

        self::assertSame($path, $instance->jsonSerialize());

        self::assertInstanceOf(PathInterface::class, $instance);
    }

    public static function providePathCases(): Generator
    {
        yield from [
            'current' => ['.'],
            'parent' => ['..'],
            'root' => ['/'],
            'relative' => ['path/to/file'],
            'absolute' => ['/path/to/file'],
        ];
    }
}
