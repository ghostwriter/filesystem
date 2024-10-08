<?php

declare(strict_types=1);

namespace Tests\Unit\Exception;

use Ghostwriter\Filesystem\Exception\FailedToCreateFileException;
use Ghostwriter\Filesystem\Filesystem;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\Unit\AbstractTestCase;

#[CoversClass(FailedToCreateFileException::class)]
#[CoversClass(Filesystem::class)]
final class FailedToCreateFileExceptionTest extends AbstractTestCase
{
    public function testExample(): void
    {
        self::assertTrue(true);
    }
}
