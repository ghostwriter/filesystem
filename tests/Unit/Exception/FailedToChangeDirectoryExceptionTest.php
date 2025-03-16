<?php

declare(strict_types=1);

namespace Tests\Unit\Exception;

use Ghostwriter\Filesystem\Exception\FailedToChangeDirectoryException;
use Ghostwriter\Filesystem\Filesystem;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(FailedToChangeDirectoryException::class)]
#[CoversClass(Filesystem::class)]
final class FailedToChangeDirectoryExceptionTest extends TestCase
{
    public function testChangeDirectory(): void
    {
        $this->expectException(FailedToChangeDirectoryException::class);
        $this->expectExceptionMessage('chdir(): No such file or directory (errno 2)');

        Filesystem::new()->chdir('non-existent-directory');
    }
}
