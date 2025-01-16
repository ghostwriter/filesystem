<?php

declare(strict_types=1);

namespace Ghostwriter\Filesystem\Exception;

use Ghostwriter\Filesystem\Interface\FilesystemExceptionInterface;

final class ErrorException extends \ErrorException implements FilesystemExceptionInterface {}
