<?php

declare(strict_types=1);

namespace Tests\Unit\Path;

use Ghostwriter\Filesystem\Path\File;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(File::class)]
final class FileTest extends TestCase
{
    public function testExample(): void
    {
        self::assertTrue(true);
    }
}
