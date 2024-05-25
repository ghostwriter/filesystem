<?php

declare(strict_types=1);

namespace Tests\Unit\Exception;

use Ghostwriter\Filesystem\Exception\FileIsNotWritableException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FileIsNotWritableException::class)]
final class FileIsNotWritableExceptionTest extends TestCase
{
    public function testExample(): void
    {
        self::assertTrue(true);
    }
}
