<?php

declare(strict_types=1);

namespace Tests\Unit\Exception;

use Ghostwriter\Filesystem\Exception\FailedToDetermineCurrentWorkingDirectoryException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FailedToDetermineCurrentWorkingDirectoryException::class)]
final class FailedToDetermineCurrentWorkingDirectoryExceptionTest extends TestCase
{
    public function testExample(): void
    {
        self::assertTrue(true);
    }
}
