<?php

declare(strict_types=1);

namespace Ghostwriter\Filesystem\Path;

use Ghostwriter\Filesystem\Interface\Path\FileInterface;
use Ghostwriter\Filesystem\Trait\PathTrait;

final readonly class File implements FileInterface
{
    use PathTrait;
}
