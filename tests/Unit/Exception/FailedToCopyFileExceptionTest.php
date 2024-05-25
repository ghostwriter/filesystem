<?php

declare(strict_types=1);

namespace Tests\Unit\Exception;

use Ghostwriter\Filesystem\Exception\FailedToCopyFileException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FailedToCopyFileException::class)]
final class FailedToCopyFileExceptionTest extends TestCase
{
    public function testExample(): void
    {
        self::assertTrue(true);
    }
}
