<?php

declare(strict_types=1);

namespace Tests\Unit\Path;

use Ghostwriter\Filesystem\Path\Link;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Link::class)]
final class LinkTest extends TestCase
{
    public function testExample(): void
    {
        self::assertTrue(true);
    }
}
