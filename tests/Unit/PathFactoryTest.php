<?php

declare(strict_types=1);

namespace Tests\Unit;

use Ghostwriter\Filesystem\PathFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(PathFactory::class)]
final class PathFactoryTest extends TestCase
{
    public function testExample(): void
    {
        self::assertTrue(true);
    }
}
