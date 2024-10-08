<?php

declare(strict_types=1);

namespace Tests\Unit\Exception;

use Ghostwriter\Filesystem\Exception\FailedToChangePermissionsException;
use Ghostwriter\Filesystem\Filesystem;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Unit\AbstractTestCase;
use Throwable;

#[CoversClass(FailedToChangePermissionsException::class)]
#[CoversClass(Filesystem::class)]
final class FailedToChangePermissionsExceptionTest extends AbstractTestCase
{
    /**
     * @throws Throwable
     */
    #[DataProvider('provideChmodCases')]
    public function testChmod(string $path): void
    {
        $this->expectException(FailedToChangePermissionsException::class);
        $this->expectExceptionMessage('chmod(): No such file or directory');

        Filesystem::new()->chmod($path, 0o644);
    }

    /**
     * @throws Throwable
     */
    public static function provideChmodCases(): iterable
    {
        yield from [
            'non-existent-file.txt' => ['non-existent-file.txt'],
        ];
    }
}
