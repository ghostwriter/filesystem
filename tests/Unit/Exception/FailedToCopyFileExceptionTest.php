<?php

declare(strict_types=1);

namespace Tests\Unit\Exception;

use Ghostwriter\Filesystem\Exception\FailedToCopyFileException;
use Ghostwriter\Filesystem\Filesystem;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\Unit\AbstractTestCase;

#[CoversClass(FailedToCopyFileException::class)]
#[CoversClass(Filesystem::class)]
final class FailedToCopyFileExceptionTest extends AbstractTestCase
{
    public function testExample(): void
    {
        self::assertTrue(true);
    }
}
