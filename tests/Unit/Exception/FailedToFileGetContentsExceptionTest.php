<?php

declare(strict_types=1);

namespace Tests\Unit\Exception;

use Ghostwriter\Filesystem\Exception\FailedToFileGetContentsException;
use Ghostwriter\Filesystem\Filesystem;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\Unit\AbstractTestCase;

#[CoversClass(FailedToFileGetContentsException::class)]
#[CoversClass(Filesystem::class)]
final class FailedToFileGetContentsExceptionTest extends AbstractTestCase
{
    public function testExample(): void
    {
        self::assertTrue(true);
    }
}
