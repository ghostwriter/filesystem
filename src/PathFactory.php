<?php

declare(strict_types=1);

namespace Ghostwriter\Filesystem;

use Ghostwriter\Filesystem\Exception\InvalidPathException;
use Ghostwriter\Filesystem\Interface\Path\DirectoryInterface;
use Ghostwriter\Filesystem\Interface\Path\FileInterface;
use Ghostwriter\Filesystem\Interface\Path\LinkInterface;
use Ghostwriter\Filesystem\Interface\PathFactoryInterface;
use Ghostwriter\Filesystem\Interface\PathInterface;
use Ghostwriter\Filesystem\Path\Directory;
use Ghostwriter\Filesystem\Path\File;
use Ghostwriter\Filesystem\Path\Link;
use SplFileInfo;
use Override;

final readonly class PathFactory implements PathFactoryInterface
{
    #[Override]
    public function create(SplFileInfo|string $path): PathInterface
    {
        $fileInfo = match (true) {
            $path instanceof SplFileInfo => $path,
            default => new SplFileInfo($path)
        };

        return match (true) {
            $fileInfo->isLink() => $this->createLink($fileInfo),
            $fileInfo->isDir() => $this->createDirectory($fileInfo),
            $fileInfo->isFile() => $this->createFile($fileInfo),
            default => throw new InvalidPathException($fileInfo->getPathname()),
        };
    }

    #[Override]
    public function createLink(SplFileInfo $fileInfo): LinkInterface
    {
        return new Link($fileInfo);
    }

    #[Override]
    public function createDirectory(SplFileInfo $fileInfo): DirectoryInterface
    {
        return new Directory($fileInfo);
    }

    #[Override]
    public function createFile(SplFileInfo $fileInfo): FileInterface
    {
        return new File($fileInfo);
    }
}
