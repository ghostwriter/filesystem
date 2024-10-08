<?php

declare(strict_types=1);

namespace Tests\Unit\Exception;

use Ghostwriter\Filesystem\Exception\FailedToDeleteFileException;
use Ghostwriter\Filesystem\Filesystem;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\Unit\AbstractTestCase;

#[CoversClass(FailedToDeleteFileException::class)]
#[CoversClass(Filesystem::class)]
final class FailedToDeleteFileExceptionTest extends AbstractTestCase
{
    public function testExample(): void
    {
        self::assertTrue(true);
    }
}
