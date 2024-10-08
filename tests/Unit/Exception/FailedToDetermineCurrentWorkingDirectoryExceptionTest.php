<?php

declare(strict_types=1);

namespace Tests\Unit\Exception;

use Ghostwriter\Filesystem\Exception\FailedToDetermineCurrentWorkingDirectoryException;
use Ghostwriter\Filesystem\Filesystem;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\Unit\AbstractTestCase;

#[CoversClass(Filesystem::class)]
#[CoversClass(FailedToDetermineCurrentWorkingDirectoryException::class)]
final class FailedToDetermineCurrentWorkingDirectoryExceptionTest extends AbstractTestCase
{
    public function testExample(): void
    {
        self::assertTrue(true);
    }
}
