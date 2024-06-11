<?php

declare(strict_types=1);

namespace Ghostwriter\Filesystem\Interface;

use Generator;
use SplFileInfo;

interface FilesystemInterface
{
    public function append(string $path, string $contents): int;

    public function basename(string $path, string $suffix = ''): string;

    public function cleanDirectory(string $path): void;

    public function copy(string $source, string $destination): void;

    public function createDirectory(string $path): void;

    public function createFile(string $path, string $contents = ''): void;

    public function createTemporaryDirectory(string $prefix): string;

    public function createTemporaryFile(string $prefix): string;

    public function currentWorkingDirectory(string ...$paths): string;

    public function delete(string $path): void;

    public function deleteDirectory(string $path): void;

    public function deleteFile(string $path): void;

    public function deleteLink(string $path): void;

    public function exists(string $path): bool;

    public function extension(string $path): string;

    public function filename(string $path): string;

    /**
     * @return Generator<SplFileInfo>
     */
    public function findIn(string $path): Generator;

    public function isDirectory(string $path): bool;

    public function isExecutable(string $path): bool;

    public function isFile(string $path): bool;

    public function isLink(string $path): bool;

    public function isReadable(string $path): bool;

    public function isWritable(string $path): bool;

    public function lastAccessTime(string $path): int;

    public function lastChangeTime(string $path): int;

    public function lastModifiedTime(string $path): int;

    public function link(string $target, string $link): void;

    /**
     * @return array<array-key, int>
     */
    public function linkInfo(string $path): array;

    public function linkTarget(string $path): string;

    public function listDirectory(string $path): Generator;

    public function missing(string $path): bool;

    public function move(string $source, string $destination): void;

    /**
     * @param int<1,max> $levels
     */
    public function parentDirectory(string $path, int $levels = 1): string;

    public function pathname(string $path): string;

    public function prepend(string $path, string $contents): int;

    public function read(string $path): string;

    public function realpath(string $path): string;

    /**
     * @return Generator<PathInterface>
     */
    public function recursiveIterator(string $directory): Generator;

    public function relative(string $from, string $to): string;

    public function size(string $path): int;

    public function write(string $path, string $contents): int;
}
