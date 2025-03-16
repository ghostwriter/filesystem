<?php

declare(strict_types=1);

namespace Tests\Unit\Exception;

use Ghostwriter\Filesystem\Exception\FailedToAppendFileException;
use Ghostwriter\Filesystem\Filesystem;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\Unit\AbstractTestCase;
use Throwable;

#[CoversClass(FailedToAppendFileException::class)]
#[CoversClass(Filesystem::class)]
final class FailedToAppendFileExceptionTest extends AbstractTestCase
{
    /**
     * @throws Throwable
     */
    public function testAppend(): void
    {
        $filesystem = Filesystem::new();

        $file = self::$temporaryDirectory . 'file.txt';

        self::assertFileDoesNotExist($file);

        $contents = '#BlackLivesMatter';

        $this->expectException(FailedToAppendFileException::class);
        $this->expectExceptionMessage($file);

        $filesystem->append($file, $contents);
    }
}
