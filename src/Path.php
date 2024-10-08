<?php

declare(strict_types=1);

namespace Ghostwriter\Filesystem;

use Ghostwriter\Filesystem\Exception\InvalidPathException;
use Ghostwriter\Filesystem\Interface\FilesystemExceptionInterface;
use Ghostwriter\Filesystem\Interface\PathInterface;
use Override;

final readonly class Path implements PathInterface
{
    /**
     * @param non-empty-string $path
     *
     * @throws FilesystemExceptionInterface
     */
    public function __construct(
        private string $path,
    ) {
        if (\trim($this->path) === '') {
            throw new InvalidPathException('The path cannot be empty.');
        }
    }

    /**
     * @param non-empty-string $path
     *
     * @throws FilesystemExceptionInterface
     */
    public static function new(string $path): PathInterface
    {
        return new self($path);
    }

    /**
     * @return non-empty-string
     */
    #[Override]
    public function __toString(): string
    {
        return $this->path;
    }

    /**
     * @return non-empty-string
     */
    #[Override]
    public function jsonSerialize(): string
    {
        return $this->path;
    }

    /**
     * @return non-empty-string
     */
    #[Override]
    public function toString(): string
    {
        return $this->path;
    }
}
