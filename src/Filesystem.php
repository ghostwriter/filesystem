<?php

declare(strict_types=1);

namespace Ghostwriter\Filesystem;

use FilesystemIterator;
use Generator;
use Ghostwriter\Filesystem\Exception\DirectoryAlreadyExistsException;
use Ghostwriter\Filesystem\Exception\FailedToAppendFileException;
use Ghostwriter\Filesystem\Exception\FailedToCopyFileException;
use Ghostwriter\Filesystem\Exception\FailedToCreateDirectoryException;
use Ghostwriter\Filesystem\Exception\FailedToCreateFileException;
use Ghostwriter\Filesystem\Exception\FailedToCreateLinkException;
use Ghostwriter\Filesystem\Exception\FailedToCreateTemporaryFileException;
use Ghostwriter\Filesystem\Exception\FailedToDeleteDirectoryException;
use Ghostwriter\Filesystem\Exception\FailedToDeleteFileException;
use Ghostwriter\Filesystem\Exception\FailedToDeleteLinkException;
use Ghostwriter\Filesystem\Exception\FailedToDetermineCurrentWorkingDirectoryException;
use Ghostwriter\Filesystem\Exception\FailedToDetermineFileSizeException;
use Ghostwriter\Filesystem\Exception\FailedToDetermineLinkInfoException;
use Ghostwriter\Filesystem\Exception\FailedToDetermineRealPathException;
use Ghostwriter\Filesystem\Exception\FailedToFileGetContentsException;
use Ghostwriter\Filesystem\Exception\FailedToFilePutContentsException;
use Ghostwriter\Filesystem\Exception\FailedToReadLinkException;
use Ghostwriter\Filesystem\Exception\FailedToRenamePathException;
use Ghostwriter\Filesystem\Exception\FileAlreadyExistsException;
use Ghostwriter\Filesystem\Exception\FileDoesNotExistException;
use Ghostwriter\Filesystem\Exception\FileIsNotReadableException;
use Ghostwriter\Filesystem\Exception\FileIsNotWritableException;
use Ghostwriter\Filesystem\Interface\FilesystemExceptionInterface;
use Ghostwriter\Filesystem\Interface\FilesystemInterface;
use Ghostwriter\Filesystem\Interface\PathInterface;
use Override;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

use function array_pad;
use function array_shift;
use function basename;
use function copy;
use function count;
use function dirname;
use function explode;
use function file_exists;
use function file_get_contents;
use function file_put_contents;
use function fileatime;
use function filectime;
use function filemtime;
use function filesize;
use function getcwd;
use function implode;
use function is_dir;
use function is_executable;
use function is_file;
use function is_link;
use function is_readable;
use function is_writable;
use function lstat;
use function mkdir;
use function pathinfo;
use function readlink;
use function realpath;
use function rename;
use function rmdir;
use function rtrim;
use function sprintf;
use function str_replace;
use function symlink;
use function sys_get_temp_dir;
use function tempnam;
use function unlink;

use const DIRECTORY_SEPARATOR;
use const FILE_APPEND;
use const PATHINFO_EXTENSION;
use const PATHINFO_FILENAME;

final readonly class Filesystem implements FilesystemInterface
{
    public function __construct(
        private readonly PathFactory $pathFactory
    ) {}

    #[Override]
    public function basename(string $path, string $suffix = ''): string
    {
        return basename($path, $suffix);
    }

    #[Override]
    public function cleanDirectory(string $path): void
    {
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::CHILD_FIRST
        );

        /**
         * @var SplFileInfo $file
         */
        foreach ($files as $file) {
            $this->delete($file->getPathname());
        }
    }

    #[Override]
    public function copy(string $source, string $destination): void
    {
        if (! copy($source, $destination)) {
            throw new FailedToCopyFileException($source);
        }
    }

    #[Override]
    public function createDirectory(string $path): void
    {
        if (file_exists($path)) {
            throw new DirectoryAlreadyExistsException($path);
        }

        if (! mkdir($path, 0o777, true) && ! is_dir($path)) {
            throw new FailedToCreateDirectoryException($path);
        }
    }

    #[Override]
    public function createFile(string $path, string $contents = ''): void
    {
        if (file_exists($path)) {
            throw new FileAlreadyExistsException($path);
        }

        $parent = dirname($path);

        if (! file_exists($parent)) {
            $this->createDirectory($parent);
        }

        $bytes = file_put_contents($path, $contents);

        if ($bytes === false) {
            throw new FailedToCreateFileException($path);
        }
    }

    #[Override]
    public function createTemporaryDirectory(string $prefix = ''): string
    {
        // TODO: check if the prefix contains a dir separator if so throw exception,
        //        it should just be a name of the folder
        $temporaryDirectory = sprintf('%s%s%s', sys_get_temp_dir(), DIRECTORY_SEPARATOR, $prefix);

        if ($this->missing($temporaryDirectory)) {
            $this->createDirectory($temporaryDirectory);
        }

        return $temporaryDirectory;
    }

    /**
     * @return non-empty-string
     */
    #[Override]
    public function createTemporaryFile(string $prefix = ''): string
    {
        $temporaryFile = tempnam(sys_get_temp_dir(), $prefix);

        if ($temporaryFile === false) {
            throw new FailedToCreateTemporaryFileException();
        }

        return $temporaryFile;
    }

    #[Override]
    public function currentWorkingDirectory(string ...$paths): string
    {
        $cwd = getcwd();
        if ($cwd === false) {
            throw new FailedToDetermineCurrentWorkingDirectoryException();
        }

        return rtrim(sprintf(
            '%s%s%s',
            $cwd,
            DIRECTORY_SEPARATOR,
            implode(DIRECTORY_SEPARATOR, $paths)
        ), DIRECTORY_SEPARATOR);
    }

    #[Override]
    public function delete(string $path): void
    {
        match (true) {
            $this->isLink($path) => $this->deleteLink($path),
            $this->isDirectory($path) => $this->deleteDirectory($path),
            default => $this->deleteFile($path),
        };
    }

    #[Override]
    public function deleteDirectory(string $path): void
    {
        $this->cleanDirectory($path);

        if (! rmdir($path)) {
            throw new FailedToDeleteDirectoryException($path);
        }
    }

    #[Override]
    public function deleteFile(string $path): void
    {
        if (! unlink($path)) {
            throw new FailedToDeleteFileException($path);
        }
    }

    #[Override]
    public function deleteLink(string $path): void
    {
        if (! unlink($path)) {
            throw new FailedToDeleteLinkException($path);
        }
    }

    #[Override]
    public function exists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * @return Generator<SplFileInfo>
     */
    #[Override]
    public function findIn(string $path): Generator
    {
        yield from new FilesystemIterator($path, FilesystemIterator::SKIP_DOTS);
    }

    #[Override]
    public function isDirectory(string $path): bool
    {
        return is_dir($path);
    }

    #[Override]
    public function isExecutable(string $path): bool
    {
        return is_executable($path);
    }

    #[Override]
    public function isFile(string $path): bool
    {
        return is_file($path);
    }

    #[Override]
    public function isLink(string $path): bool
    {
        return is_link($path);
    }

    #[Override]
    public function isReadable(string $path): bool
    {
        return is_readable($path);
    }

    #[Override]
    public function isWritable(string $path): bool
    {
        return is_writable($path);
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function link(string $target, string $link): void
    {
        if (! symlink($target, $link)) {
            throw new FailedToCreateLinkException($link);
        }
    }

    /**
     * @throws FilesystemExceptionInterface
     *
     * @return array<array-key, int>
     */
    #[Override]
    public function linkInfo(string $path): array
    {
        $info = lstat($path);

        if ($info === false) {
            throw new FailedToDetermineLinkInfoException($path);
        }

        return $info;
    }

    #[Override]
    public function missing(string $path): bool
    {
        return ! file_exists($path);
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function move(string $source, string $destination): void
    {
        if (! rename($source, $destination)) {
            throw new FailedToRenamePathException($source);
        }
    }

    /**
     * @param int<1,max> $levels
     */
    #[Override]
    public function parentDirectory(string $path, int $levels = 1): string
    {
        return dirname($path, $levels);
    }

    #[Override]
    public function prepend(string $path, string $contents): int
    {
        return $this->write($path, $contents . $this->read($path));
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function append(string $path, string $contents): int
    {
        if (! $this->isFile($path)) {
            throw new FileDoesNotExistException($path);
        }

        if (! $this->isWritable($path)) {
            throw new FileIsNotWritableException($path);
        }

        $bytes = file_put_contents($path, $contents, FILE_APPEND);

        if ($bytes === false) {
            throw new FailedToAppendFileException($path);
        }

        return $bytes;
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function write(string $path, string $contents): int
    {
        if (! $this->isFile($path)) {
            throw new FileDoesNotExistException($path);
        }

        if (! $this->isWritable($path)) {
            throw new FileIsNotWritableException($path);
        }

        $bytes = file_put_contents($path, $contents);

        if ($bytes === false) {
            throw new FailedToFilePutContentsException($path);
        }

        return $bytes;
    }

    #[Override]
    public function read(string $path): string
    {
        if (! $this->isFile($path)) {
            throw new FileDoesNotExistException($path);
        }

        if (! $this->isReadable($path)) {
            throw new FileIsNotReadableException($path);
        }

        $contents = file_get_contents($path);
        if ($contents === false) {
            throw new FailedToFileGetContentsException($path);
        }

        return $contents;
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function linkTarget(string $path): string
    {
        $target = readlink($path);

        if ($target === false) {
            throw new FailedToReadLinkException($path);
        }

        return $target;
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function realpath(string $path): string
    {
        $target = realpath($path);

        if ($target === false) {
            throw new FailedToDetermineRealPathException($path);
        }

        return $target;
    }

    /**
     * @return Generator<PathInterface>
     */
    #[Override]
    public function recursiveIterator(string $directory): Generator
    {
        foreach (
            new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
                RecursiveIteratorIterator::LEAVES_ONLY
            ) as $path
        ) {
            if (! $path instanceof SplFileInfo) {
                continue;
            }

            yield $this->pathFactory->create($path);
        }
    }

    #[Override]
    public function relative(string $from, string $to): string
    {
        // some compatibility fixes for Windows paths
        $from = is_dir($from) ? rtrim($from, '\/') . '/' : $from;
        $to = is_dir($to) ? rtrim($to, '\/') . '/' : $to;

        $from = str_replace('\\', '/', $from);
        $to = str_replace('\\', '/', $to);

        $from = explode('/', $from);
        $to = explode('/', $to);
        $relPath = $to;

        foreach ($from as $depth => $dir) {
            // find first non-matching dir
            if ($dir === $to[$depth]) {
                // ignore this directory
                array_shift($relPath);
                continue;
            }
            // get number of remaining dirs to $from
            $remaining = count($from) - $depth;

            if ($remaining > 1) {
                // add traversals up to first matching dir
                $padLength = (count($relPath) + $remaining - 1) * -1;
                $relPath = array_pad($relPath, $padLength, '..');
                break;
            }

            $relPath[0] = './' . $relPath[0];
        }
        return implode('/', $relPath);
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function size(string $path): int
    {
        $size = filesize($path);

        if ($size === false) {
            throw new FailedToDetermineFileSizeException($path);
        }

        return $size;
    }

    //    #[Override]
    //    public function fileObject(string $path): FileInterface
    //    {
    //        return new File($path, $this);
    //    }
    //
    //    #[Override]
    //    public function directoryObject(string $path): DirectoryInterface
    //    {
    //        return new Directory($path, $this);
    //    }
    //
    //    #[Override]
    //    public function linkObject(string $path): LinkInterface
    //    {
    //        return new Link($path, $this);
    //    }

    #[Override]
    public function listDirectory(string $path): Generator
    {
        return $this->findIn($path);
    }

    #[Override]
    public function lastAccessTime(string $path): int
    {
        return fileatime($path);
    }

    #[Override]
    public function lastChangeTime(string $path): int
    {
        return filectime($path);
    }

    #[Override]
    public function lastModifiedTime(string $path): int
    {
        return filemtime($path);
    }

    #[Override]
    public function extension(string $path): string
    {
        return pathinfo($path, PATHINFO_EXTENSION);
    }

    #[Override]
    public function filename(string $path): string
    {
        return pathinfo($path, PATHINFO_FILENAME);
    }

    #[Override]
    public function pathname(string $path): string
    {
        return $path;
    }
}
