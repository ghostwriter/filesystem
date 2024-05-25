<?php

declare(strict_types=1);

namespace Ghostwriter\Filesystem\Trait;

use FilesystemIterator;
use Generator;
use Ghostwriter\Filesystem\Interface\PathInterface;
use Ghostwriter\Filesystem\Path\Directory;
use Ghostwriter\Filesystem\Exception\InvalidDirectoryPathException;
use Ghostwriter\Filesystem\Path\File;
use Ghostwriter\Filesystem\Path\Link;
use Override;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

use function dirname;

trait PathTrait
{
    public function __construct(
        private readonly SplFileInfo $splFileInfo
    ) {}

    #[Override]
    public function exists(): bool
    {
        return $this->splFileInfo->isFile() || $this->splFileInfo->isDir() || $this->splFileInfo->isLink();
    }

    #[Override]
    public function isFile(): bool
    {
        return $this->splFileInfo->isFile();
    }

    #[Override]
    public function isDirectory(): bool
    {
        return $this->splFileInfo->isDir();
    }

    #[Override]
    public function isLink(): bool
    {
        return $this->splFileInfo->isLink();
    }

    #[Override]
    public function toString(): string
    {
        return $this->splFileInfo->getRealPath() ?: $this->splFileInfo->getPathname();
    }

    #[Override]
    public function getIterator(): Generator
    {
        $path = $this->splFileInfo->getPathname();
        if (! $this->isDirectory()) {
            throw new InvalidDirectoryPathException($path);
        }

        /** @var Generator<SplFileInfo> $fileInfos */
        $fileInfos = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($fileInfos as $fileInfo) {
            if (! $fileInfo instanceof SplFileInfo) {
                continue;
            }

            if ($fileInfo->isLink()) {
                yield new Link($fileInfo);
                continue;
            }

            if ($fileInfo->isFile()) {
                yield new File($fileInfo);
                continue;
            }

            yield new Directory($fileInfo);
        }
    }

    #[Override]
    public function lastAccessTime(): int
    {
        return $this->splFileInfo->getATime();
    }

    #[Override]
    public function lastChangeTime(): int
    {
        return $this->splFileInfo->getCTime();
    }

    #[Override]
    public function lastModifiedTime(): int
    {
        return $this->splFileInfo->getMTime();
    }

    #[Override]
    public function basename(string $suffix = ''): string
    {
        return $this->splFileInfo->getBasename($suffix);
    }

    #[Override]
    public function extension(): string
    {
        return $this->splFileInfo->getExtension();
    }

    #[Override]
    public function filename(): string
    {
        return $this->splFileInfo->getFilename();
    }

    #[Override]
    public function linkTarget(): string
    {
        return $this->splFileInfo->getLinkTarget();
    }

    #[Override]
    public function parent(): PathInterface
    {
        return new Directory(new SplFileInfo(dirname($this->splFileInfo->getPathname())));
    }

    #[Override]
    public function realpath(): string
    {
        return $this->splFileInfo->getRealPath();
    }

    #[Override]
    public function size(): int
    {
        return $this->splFileInfo->getSize();
    }

    #[Override]
    public function isExecutable(): bool
    {
        return $this->splFileInfo->isExecutable();
    }

    #[Override]
    public function isReadable(): bool
    {
        return $this->splFileInfo->isReadable();
    }

    #[Override]
    public function isWritable(): bool
    {
        return $this->splFileInfo->isWritable();
    }
}
