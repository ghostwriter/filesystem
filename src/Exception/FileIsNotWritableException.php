<?php

declare(strict_types=1);

namespace Ghostwriter\Filesystem\Exception;

use Ghostwriter\Filesystem\Interface\FilesystemExceptionInterface;
use InvalidArgumentException;

final class FileIsNotWritableException extends InvalidArgumentException implements FilesystemExceptionInterface {}
