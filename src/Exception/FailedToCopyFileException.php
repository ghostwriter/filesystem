<?php

declare(strict_types=1);

namespace Ghostwriter\Filesystem\Exception;

use Ghostwriter\Filesystem\Interface\FilesystemExceptionInterface;
use RuntimeException;

final class FailedToCopyFileException extends RuntimeException implements FilesystemExceptionInterface
{
}
