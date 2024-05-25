<?php

declare(strict_types=1);

namespace Ghostwriter\Filesystem\Interface;

use IteratorAggregate;

/**
 * @extends IteratorAggregate<PathInterface>
 */
interface PathInterface extends IteratorAggregate
{
    //    public function absolutePath(): string;
    //    public function relativePath(): string;
    public function basename(string $suffix = ''): string;

    public function exists(): bool;

    public function extension(): string;

    public function filename(): string;

    public function isDirectory(): bool;

    public function isExecutable(): bool;

    public function isFile(): bool;

    public function isLink(): bool;

    public function isReadable(): bool;

    public function isWritable(): bool;

    public function lastAccessTime(): int;

    public function lastChangeTime(): int;

    public function lastModifiedTime(): int;

    public function linkTarget(): string;

    public function parent(): self;

    public function realPath(): string;

    public function size(): int;

    public function toString(): string;
}
