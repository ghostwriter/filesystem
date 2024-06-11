<?php

declare(strict_types=1);

namespace Ghostwriter\Filesystem\Interface;

use Ghostwriter\Filesystem\Interface\Path\DirectoryInterface;
use Ghostwriter\Filesystem\Interface\Path\FileInterface;
use Ghostwriter\Filesystem\Interface\Path\LinkInterface;
use SplFileInfo;

interface PathFactoryInterface
{
    public function create(string $path): PathInterface;

    public function createDirectory(SplFileInfo $fileInfo): DirectoryInterface;

    public function createFile(SplFileInfo $fileInfo): FileInterface;

    public function createLink(SplFileInfo $fileInfo): LinkInterface;
}
