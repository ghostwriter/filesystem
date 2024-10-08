<?php

declare(strict_types=1);

namespace Tests\Unit\Exception;

use Ghostwriter\Filesystem\Exception\InvalidPathException;
use Ghostwriter\Filesystem\Filesystem;
use Ghostwriter\Filesystem\Path;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\Unit\AbstractTestCase;
use Throwable;

#[CoversClass(InvalidPathException::class)]
#[CoversClass(Path::class)]
#[CoversClass(Filesystem::class)]
final class InvalidPathExceptionTest extends AbstractTestCase
{
    /**
     * @throws Throwable
     */
    public function testEmptyPathConstruct(): void
    {
        $this->expectException(InvalidPathException::class);
        $this->expectExceptionMessage('The path cannot be empty.');

        new Path(' ');
    }

    /**
     * @throws Throwable
     */
    public function testEmptyPathNew(): void
    {
        $this->expectException(InvalidPathException::class);
        $this->expectExceptionMessage('The path cannot be empty.');

        Path::new(' ');
    }
}
