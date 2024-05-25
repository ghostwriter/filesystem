<?php

declare(strict_types=1);

namespace Ghostwriter\Filesystem\Path;

use Ghostwriter\Filesystem\Interface\Path\DirectoryInterface;
use Ghostwriter\Filesystem\Trait\PathTrait;

final readonly class Directory implements DirectoryInterface
{
    use PathTrait;
}
