<?php

declare(strict_types=1);

namespace Ghostwriter\Filesystem\Interface;

use FilesystemIterator;
use Generator;
use RecursiveDirectoryIterator;
use RecursiveIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

interface FilesystemInterface
{
    public function append(string $path, string $contents): int;

    public function basename(string $path, string $suffix = ''): string;

    public function chdir(string $directory): void;

    public function chmod(string $path, int $mode): void;

    public function cleanDirectory(string $directory): void;

    public function copy(string $source, string $destination): void;

    public function createDirectory(string $path): void;

    public function createFile(string $path, string $contents = ''): int;

    public function createTemporaryDirectory(string $prefix): string;

    public function createTemporaryFile(string $prefix): string;

    public function currentWorkingDirectory(): string;

    public function delete(string $path): void;

    public function deleteDirectory(string $path): void;

    public function deleteFile(string $path): void;

    public function deleteLink(string $path): void;

    public function exists(string $path): bool;

    public function extension(string $path): string;

    public function filename(string $path): string;

    public function filesystemIterator(
        string $directory,
        int $flags = FilesystemIterator::SKIP_DOTS,
    ): FilesystemIterator;

    public function isDirectory(string $path): bool;

    public function isExecutable(string $path): bool;

    public function isFile(string $path): bool;

    public function isLink(string $path): bool;

    public function isReadable(string $path): bool;

    public function isWritable(string $path): bool;

    public function lastAccessTime(string $path): int;

    public function lastChangeTime(string $path): int;

    public function lastModifiedTime(string $path): int;

    public function linkTarget(string $path): string;

    public function listDirectory(string $path): Generator;

    public function missing(string $path): bool;

    public function move(string $source, string $destination): void;

    /**
     * @param int<1,max> $levels
     */
    public function parentDirectory(string $path, int $levels = 1): string;

    public function pathname(string $path): string;

    public function permissions(string $path): string;

    public function prepend(string $path, string $contents): int;

    public function read(string $path): string;

    public function realpath(string $path): string;

    public function recursiveDirectoryIterator(
        string $directory,
        int $mode = FilesystemIterator::SKIP_DOTS,
    ): RecursiveDirectoryIterator;

    /**
     * @return Generator<PathInterface>
     */
    public function recursiveIterator(string $directory): Generator;

    public function recursiveIteratorIterator(
        RecursiveIterator $iterator,
        int $mode = RecursiveIteratorIterator::CHILD_FIRST,
    ): RecursiveIteratorIterator;

    public function recursiveRegexIterator(
        RecursiveIterator $iterator,
        string $pattern,
        int $mode = RegexIterator::GET_MATCH,
    ): RecursiveRegexIterator;

    public function regexIterator(string $directory, string $pattern): RegexIterator;

    public function relative(string $from, string $to): string;

    public function size(string $path): int;

    public function symlink(string $target, string $link): void;

    public function temporaryDirectory(): string;

    public function touch(string $path): void;

    public function write(string $path, string $contents): int;
}
