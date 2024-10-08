<?php

declare(strict_types=1);

namespace Ghostwriter\Filesystem\Interface;

use JsonSerializable;
use Stringable;

interface PathInterface extends JsonSerializable, Stringable
{
    /**
     * @return non-empty-string
     */
    public function toString(): string;
}
