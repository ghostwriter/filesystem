<?php

declare(strict_types=1);

namespace Ghostwriter\Filesystem\Exception;

use Ghostwriter\Filesystem\Interface\FilesystemExceptionInterface;
use LogicException;

final class ShouldNotHappenException extends LogicException implements FilesystemExceptionInterface {}
