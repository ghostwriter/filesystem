<?php

declare(strict_types=1);

namespace Ghostwriter\Filesystem\Path;

use Ghostwriter\Filesystem\Interface\Path\LinkInterface;
use Ghostwriter\Filesystem\Trait\PathTrait;

final readonly class Link implements LinkInterface
{
    use PathTrait;
}
