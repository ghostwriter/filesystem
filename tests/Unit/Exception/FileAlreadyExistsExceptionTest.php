<?php

declare(strict_types=1);

namespace Tests\Unit\Exception;

use Ghostwriter\Filesystem\Exception\FileAlreadyExistsException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FileAlreadyExistsException::class)]
final class FileAlreadyExistsExceptionTest extends TestCase
{
    public function testExample(): void
    {
        self::assertTrue(true);
    }
}
