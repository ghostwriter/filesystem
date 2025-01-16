<?php

declare(strict_types=1);

namespace Ghostwriter\Filesystem;

use Closure;
use FilesystemIterator;
use Generator;
use Ghostwriter\Filesystem\Exception\DestinationAlreadyExistsException;
use Ghostwriter\Filesystem\Exception\DirectoryAlreadyExistsException;
use Ghostwriter\Filesystem\Exception\DirectoryDoesNotExistException;
use Ghostwriter\Filesystem\Exception\ErrorException;
use Ghostwriter\Filesystem\Exception\FailedToAppendFileException;
use Ghostwriter\Filesystem\Exception\FailedToChangeDirectoryException;
use Ghostwriter\Filesystem\Exception\FailedToChangePermissionsException;
use Ghostwriter\Filesystem\Exception\FailedToCleanDirectoryException;
use Ghostwriter\Filesystem\Exception\FailedToCopyFileException;
use Ghostwriter\Filesystem\Exception\FailedToCreateDirectoryException;
use Ghostwriter\Filesystem\Exception\FailedToCreateFileException;
use Ghostwriter\Filesystem\Exception\FailedToCreateLinkException;
use Ghostwriter\Filesystem\Exception\FailedToCreateTemporaryDirectoryException;
use Ghostwriter\Filesystem\Exception\FailedToCreateTemporaryFileException;
use Ghostwriter\Filesystem\Exception\FailedToDeleteDirectoryException;
use Ghostwriter\Filesystem\Exception\FailedToDeleteFileException;
use Ghostwriter\Filesystem\Exception\FailedToDeleteLinkException;
use Ghostwriter\Filesystem\Exception\FailedToDetermineCurrentWorkingDirectoryException;
use Ghostwriter\Filesystem\Exception\FailedToDetermineFileSizeException;
use Ghostwriter\Filesystem\Exception\FailedToDetermineRealPathException;
use Ghostwriter\Filesystem\Exception\FailedToFileGetContentsException;
use Ghostwriter\Filesystem\Exception\FailedToFilePutContentsException;
use Ghostwriter\Filesystem\Exception\FailedToGlobException;
use Ghostwriter\Filesystem\Exception\FailedToPrependFileException;
use Ghostwriter\Filesystem\Exception\FailedToReadLinkException;
use Ghostwriter\Filesystem\Exception\FailedToRenamePathException;
use Ghostwriter\Filesystem\Exception\FileAlreadyExistsException;
use Ghostwriter\Filesystem\Exception\FileDoesNotExistException;
use Ghostwriter\Filesystem\Exception\FileIsNotReadableException;
use Ghostwriter\Filesystem\Exception\FileIsNotWritableException;
use Ghostwriter\Filesystem\Exception\LinkDoesNotExistException;
use Ghostwriter\Filesystem\Exception\ShouldNotHappenException;
use Ghostwriter\Filesystem\Exception\SourceDoesNotExistException;
use Ghostwriter\Filesystem\Interface\FilesystemExceptionInterface;
use Ghostwriter\Filesystem\Interface\FilesystemInterface;
use Override;
use RecursiveDirectoryIterator;
use RecursiveIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;
use SplFileInfo;
use Throwable;

use const DIRECTORY_SEPARATOR;
use const GLOB_ONLYDIR;
use const PATHINFO_EXTENSION;
use const PATHINFO_FILENAME;

use function array_merge;
use function array_pad;
use function array_reduce;
use function array_shift;
use function basename;
use function chdir;
use function chmod;
use function clearstatcache;
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
use function fileperms;
use function filesize;
use function getcwd;
use function glob;
use function implode;
use function is_a;
use function is_dir;
use function is_executable;
use function is_file;
use function is_link;
use function is_readable;
use function is_writable;
use function mb_rtrim;
use function mb_trim;
use function mkdir;
use function pathinfo;
use function readlink;
use function realpath;
use function rename;
use function restore_error_handler;
use function rmdir;
use function set_error_handler;
use function sprintf;
use function str_replace;
use function symlink;
use function sys_get_temp_dir;
use function tempnam;
use function touch;
use function unlink;

final class Filesystem implements FilesystemInterface
{
    public static function new(): self
    {
        return new self();
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function append(string $path, string $contents): int
    {
        return $this->safely(
            static fn (
                FilesystemInterface $filesystem
            ): int => $filesystem->write($path, $filesystem->read($path) . $contents),
            FailedToAppendFileException::class
        );

    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function basename(string $path, string $suffix = ''): string
    {
        return $this->safely(static fn (): string => basename($path, $suffix));
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function chdir(string $directory): void
    {
        $this->safely(static function () use ($directory): void {
            $changed = chdir($directory);

            if (false === $changed) {
                throw new FailedToChangeDirectoryException($directory);
            }
        }, FailedToChangeDirectoryException::class);
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function chmod(string $path, int $mode): void
    {
        $this->safely(static function () use ($path, $mode): void {
            $changed = @chmod($path, $mode);

            if (false === $changed) {
                throw new FailedToChangePermissionsException($path);
            }
        }, FailedToChangePermissionsException::class);
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function cleanDirectory(string $directory): void
    {
        $this->safely(static function (FilesystemInterface $filesystem) use ($directory): void {
            foreach ($filesystem->recursiveIterator($directory) as $path => $fileInfo) {
                $filesystem->delete($path);
            }
        }, FailedToCleanDirectoryException::class);
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function copy(string $source, string $destination): void
    {
        $this->safely(static function (FilesystemInterface $filesystem) use ($source, $destination): void {
            if ($filesystem->missing($source)) {
                throw new SourceDoesNotExistException($source);
            }

            if ($filesystem->exists($destination)) {
                throw new DestinationAlreadyExistsException($destination);
            }

            $copied = copy($source, $destination);

            if (false === $copied) {
                throw new FailedToCopyFileException(
                    sprintf('Could not copy file: %s to %s', $source, $destination)
                );
            }
        }, FailedToCopyFileException::class);
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function createDirectory(string $path, int $mode = 0o777, bool $recursive = true): void
    {
        $this->safely(static function (FilesystemInterface $filesystem) use ($path, $mode, $recursive): void {
            if ($filesystem->isDirectory($path)) {
                throw new DirectoryAlreadyExistsException('Directory already exists: ' . $path);
            }

            $created = mkdir($path, $mode, $recursive);

            if (false === $created && ! $filesystem->isDirectory($path)) {
                throw new FailedToCreateDirectoryException('Could not create directory: ' . $path);
            }
        }, FailedToCreateDirectoryException::class);
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function createFile(string $path, string $contents = ''): int
    {
        return $this->safely(
            static function (FilesystemInterface $filesystem) use ($path, $contents): int {
                $filesystem->touch($path);

                if (mb_trim($contents) === '') {
                    return 0;
                }

                return $filesystem->write($path, $contents);
            },
            FailedToCreateFileException::class
        );
    }

    /**
     * @throws FilesystemExceptionInterface
     *
     * @return non-empty-string
     */
    #[Override]
    public function createTemporaryDirectory(string $prefix = ''): string
    {
        return $this->safely(
            /** @return non-empty-string */
            static function (FilesystemInterface $filesystem) use ($prefix): string {
                $temporaryDirectory = $filesystem->temporaryDirectory();

                $temporaryDirectory = sprintf('%s%s%s', $temporaryDirectory, DIRECTORY_SEPARATOR, $prefix);

                if ($filesystem->missing($temporaryDirectory)) {
                    $filesystem->createDirectory($temporaryDirectory);
                }

                return $temporaryDirectory;
            },
            FailedToCreateTemporaryDirectoryException::class
        );
    }

    /**
     * @throws FilesystemExceptionInterface
     *
     * @return non-empty-string
     */
    #[Override]
    public function createTemporaryFile(string $prefix = ''): string
    {
        return $this->safely(
            /** @return non-empty-string */
            static function (FilesystemInterface $filesystem) use ($prefix): string {
                $temporaryDirectory = $filesystem->temporaryDirectory();

                /** @var false|non-empty-string $temporaryFile */
                $temporaryFile = tempnam($temporaryDirectory, $prefix);

                if (false === $temporaryFile) {
                    throw new FailedToCreateTemporaryFileException();
                }

                return $temporaryFile;
            },
            FailedToCreateTemporaryFileException::class
        );
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function currentWorkingDirectory(): string
    {
        return $this->safely(
            /** @return non-empty-string */
            static function (): string {
                $workingDirectory = getcwd();

                if (false === $workingDirectory) {
                    throw new FailedToDetermineCurrentWorkingDirectoryException();
                }

                return $workingDirectory;
            },
            FailedToDetermineCurrentWorkingDirectoryException::class
        );
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function delete(string $path): void
    {
        $currentWorkingDirectory = $this->currentWorkingDirectory();

        $this->chdir($this->parentDirectory($path));

        match (true) {
            $this->isLink($path) => $this->deleteLink($path),
            $this->isDirectory($path) => $this->deleteDirectory($path),
            default => $this->deleteFile($path),
        };

        $this->chdir($currentWorkingDirectory);
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function deleteDirectory(string $path): void
    {
        $this->safely(static function (FilesystemInterface $filesystem) use ($path): void {
            if (! $filesystem->isDirectory($path)) {
                throw new DirectoryDoesNotExistException($path);
            }

            $filesystem->cleanDirectory($path);

            $deleted = rmdir($path);

            if (false === $deleted) {
                throw new FailedToDeleteDirectoryException('Could not delete directory: ' . $path);
            }
        }, FailedToDeleteDirectoryException::class);
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function deleteFile(string $path): void
    {
        $this->safely(static function (FilesystemInterface $filesystem) use ($path): void {
            if (! $filesystem->isFile($path)) {
                throw new FileDoesNotExistException($path);
            }

            $deleted = unlink($path);

            if (false === $deleted) {
                throw new FailedToDeleteFileException($path);
            }
        }, FailedToDeleteFileException::class);
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function deleteLink(string $path): void
    {
        $this->safely(static function (FilesystemInterface $filesystem) use ($path): void {
            if (! $filesystem->isLink($path)) {
                throw new LinkDoesNotExistException($path);
            }

            $deleted = unlink($path);

            if (false === $deleted) {
                throw new FailedToDeleteLinkException($path);
            }
        }, FailedToDeleteLinkException::class);
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function exists(string $path): bool
    {
        return $this->safely(static fn (): bool => file_exists($path));
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function extension(string $path): string
    {
        return $this->safely(static fn (): string => pathinfo($path, PATHINFO_EXTENSION));
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function filename(string $path): string
    {
        return $this->safely(static fn (): string => pathinfo($path, PATHINFO_FILENAME));
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function filesystemIterator(
        string $directory,
        int $flags = FilesystemIterator::SKIP_DOTS,
    ): FilesystemIterator {
        return $this->safely(static fn (): FilesystemIterator => new FilesystemIterator($directory, $flags));
    }

    /**
     * @throws FilesystemExceptionInterface
     *
     * @return list<non-empty-string>
     */
    #[Override]
    public function glob(string $pattern, int $flags = 0): array
    {
        return $this->safely(
            /**
             * @return list<non-empty-string>
             */
            static function () use ($pattern, $flags): array {
                /** @var false|list<non-empty-string> $result */
                $result = glob($pattern, $flags);

                if (false === $result) {
                    throw new FailedToGlobException($pattern);
                }

                return $result;
            },
            FailedToGlobException::class
        );
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function isDirectory(string $path): bool
    {
        return $this->safely(static fn (): bool => is_dir($path));
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function isExecutable(string $path): bool
    {
        return $this->safely(static fn (): bool => is_executable($path));
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function isFile(string $path): bool
    {
        return $this->safely(static fn (): bool => is_file($path));
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function isLink(string $path): bool
    {
        return $this->safely(static fn (): bool => is_link($path));
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function isReadable(string $path): bool
    {
        return $this->safely(static fn (): bool => is_readable($path));
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function isWritable(string $path): bool
    {
        return $this->safely(static fn (): bool => is_writable($path));
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function lastAccessTime(string $path): int
    {
        return $this->safely(static fn (): int => fileatime($path));
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function lastChangeTime(string $path): int
    {
        return $this->safely(static fn (): int => filectime($path));
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function lastModifiedTime(string $path): int
    {
        return $this->safely(static fn (): int => filemtime($path));
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function linkTarget(string $path): string
    {
        return $this->safely(
            static function () use ($path): string {
                $target = readlink($path);

                if (false === $target) {
                    throw new FailedToReadLinkException($path);
                }

                return $target;
            },
            FailedToReadLinkException::class
        );
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function listDirectory(string $path): Generator
    {
        yield from $this->filesystemIterator($path);
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function missing(string $path): bool
    {
        return $this->safely(static fn (): bool => ! file_exists($path));
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function move(string $source, string $destination): void
    {
        $this->safely(static function (FilesystemInterface $filesystem) use ($source, $destination): void {
            if ($filesystem->missing($source)) {
                throw new ShouldNotHappenException('Source file does not exist: ' . $source);
            }

            if ($filesystem->exists($destination)) {
                throw new ShouldNotHappenException('Destination file already exists: ' . $destination);
            }

            $moved = rename($source, $destination);

            if (false === $moved) {
                throw new FailedToRenamePathException(sprintf('Could not move file: %s to %s', $source, $destination));
            }
        }, FailedToRenamePathException::class);
    }

    /**
     * @param int<1,max> $levels
     *
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function parentDirectory(string $path, int $levels = 1): string
    {
        return $this->safely(static fn (): string => dirname($path, $levels));
    }

    #[Override]
    public function pathname(string $path): string
    {
        // TODO: \pathinfo() *face-palm*
        return $path;
    }

    /**
     * @throws FilesystemExceptionInterface
     *
     * @return non-empty-string
     */
    #[Override]
    public function permissions(string $path): string
    {
        return $this->safely(
            /** @return non-empty-string */
            static function () use ($path): string {
                clearstatcache(true, $path);

                $permissions = fileperms($path);

                if (false === $permissions) {
                    throw new ShouldNotHappenException('Could not determine permissions for: ' . $path);
                }

                return sprintf('%o', $permissions & 0o777);
            }
        );
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function prepend(string $path, string $contents): int
    {
        return $this->safely(
            static fn (
                FilesystemInterface $filesystem
            ): int => $filesystem->write($path, $contents . $filesystem->read($path)),
            FailedToPrependFileException::class
        );
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function read(string $path): string
    {
        return $this->safely(static function (FilesystemInterface $filesystem) use ($path): string {
            if ($filesystem->missing($path)) {
                throw new FileDoesNotExistException($path);
            }

            if (! $filesystem->isReadable($path)) {
                throw new FileIsNotReadableException($path);
            }

            $contents = file_get_contents($path);
            if (false === $contents) {
                throw new FailedToFileGetContentsException($path);
            }

            return $contents;
        }, FailedToFileGetContentsException::class);
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function realpath(string $path): string
    {
        return $this->safely(static function () use ($path): string {
            $target = realpath($path);

            if (false === $target) {
                throw new FailedToDetermineRealPathException($path);
            }

            return $target;
        }, FailedToDetermineRealPathException::class);
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function recursiveDirectoryIterator(
        string $directory,
        int $mode = FilesystemIterator::SKIP_DOTS,
    ): RecursiveDirectoryIterator {
        return $this->safely(
            static fn (): RecursiveDirectoryIterator => new RecursiveDirectoryIterator($directory, $mode),
        );
    }

    /**
     * @throws FilesystemExceptionInterface
     *
     * @return Generator<non-empty-string,SplFileInfo>
     */
    #[Override]
    public function recursiveIterator(string $directory): Generator
    {
        yield from $this->safely(
            static fn (FilesystemInterface $filesystem): RecursiveIteratorIterator => $filesystem->recursiveIteratorIterator(
                $filesystem->recursiveDirectoryIterator($directory)
            )
        );
    }

    /**
     * @throws FilesystemExceptionInterface
     *
     * @return RecursiveIteratorIterator<RecursiveIterator>
     */
    #[Override]
    public function recursiveIteratorIterator(
        RecursiveIterator $iterator,
        int $mode = RecursiveIteratorIterator::CHILD_FIRST,
    ): RecursiveIteratorIterator {
        return $this->safely(
            static fn (): RecursiveIteratorIterator => new RecursiveIteratorIterator($iterator, $mode)
        );
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function recursiveRegexIterator(
        RecursiveIterator $iterator,
        string $pattern,
        int $mode = RegexIterator::MATCH,
    ): RecursiveRegexIterator {
        return $this->safely(
            static fn (): RecursiveRegexIterator => new RecursiveRegexIterator($iterator, $pattern, $mode)
        );
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function regexIterator(string $directory, string $pattern): RegexIterator
    {
        return $this->safely(
            static fn (FilesystemInterface $filesystem): RegexIterator => new RegexIterator(
                $filesystem->recursiveIteratorIterator($filesystem->recursiveDirectoryIterator($directory)),
                $pattern,
            )
        );
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function relative(string $from, string $to): string
    {
        return $this->safely(static function () use ($from, $to): string {
            // some compatibility fixes for Windows paths
            $from = is_dir($from) ? mb_rtrim($from, '\/') . '/' : $from;
            $to = is_dir($to) ? mb_rtrim($to, '\/') . '/' : $to;

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

                if (1 < $remaining) {
                    // add traversals up to first matching dir
                    $padLength = (count($relPath) + $remaining - 1) * -1;
                    $relPath = array_pad($relPath, $padLength, '..');

                    break;
                }

                $relPath[0] = './' . $relPath[0];
            }

            return implode('/', $relPath);
        });
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function size(string $path): int
    {
        return $this->safely(static function () use ($path): int {
            $size = filesize($path);

            if (false === $size) {
                throw new FailedToDetermineFileSizeException($path);
            }

            return $size;
        }, FailedToDetermineFileSizeException::class);
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function symlink(string $target, string $link): void
    {
        $this->safely(static function () use ($target, $link): void {
            $symlinked = symlink($target, $link);

            if (false === $symlinked) {
                throw new FailedToCreateLinkException($link);
            }
        }, FailedToCreateLinkException::class);
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function temporaryDirectory(): string
    {
        return $this->safely(static fn (): string => sys_get_temp_dir());
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function touch(string $path): void
    {
        $this->safely(static function (FilesystemInterface $filesystem) use ($path): void {
            if ($filesystem->exists($path)) {
                if ($filesystem->isFile($path)) {
                    throw new FileAlreadyExistsException($path);
                }

                throw new ShouldNotHappenException('Path already exists and is not a file: ' . $path);
            }

            $parentDirectory = $filesystem->parentDirectory($path);

            if (! $filesystem->isDirectory($parentDirectory)) {
                $filesystem->createDirectory($parentDirectory);
            }

            $touched = touch($path);

            if (false === $touched) {
                throw new FailedToCreateFileException($path);
            }
        }, FailedToCreateFileException::class);
    }

    /**
     * @throws FilesystemExceptionInterface
     */
    #[Override]
    public function write(string $path, string $contents): int
    {
        return $this->safely(static function (FilesystemInterface $filesystem) use ($path, $contents): int {
            if ($filesystem->missing($path)) {
                return $filesystem->createFile($path, $contents);
            }

            if (! $filesystem->isWritable($path)) {
                throw new FileIsNotWritableException($path);
            }

            $bytesWritten = file_put_contents($path, $contents);

            if (false === $bytesWritten) {
                throw new FailedToFilePutContentsException($path);
            }

            return $bytesWritten;
        }, FailedToFilePutContentsException::class);
    }

    /**
     * @param non-empty-string $pattern
     *
     * @throws FilesystemExceptionInterface
     *
     * @return list<non-empty-string>
     */
    private function findPhpFilesUsingGlob(string $pattern): array
    {
        /**
         * @var list<non-empty-string>
         */
        return array_reduce(
            /**
             * Recursively search in subdirectories.
             */
            $this->glob($pattern . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR),
            fn (array $carry, string $subDirectory): array
            /**
             * Merge the results from subdirectories.
             *
             * @var non-empty-string $subDirectory
             */
            => array_merge($carry, $this->findPhpFilesUsingGlob($subDirectory)),
            /**
             * Search for php files in the current directory.
             */
            $this->glob($pattern . DIRECTORY_SEPARATOR . '*.php'),
        );
    }

    /**
     * @template TMixed
     *
     * @param Closure(FilesystemInterface):TMixed $function
     *
     * @throws FilesystemExceptionInterface
     *
     * @return TMixed
     */
    private function safely(Closure $function, string $class = ShouldNotHappenException::class): mixed
    {
        if (! is_a($class, FilesystemExceptionInterface::class, true)) {
            throw new ShouldNotHappenException(
                sprintf('Class "%s" MUST implement "%s".', $class, FilesystemExceptionInterface::class)
            );
        }

        try {
            set_error_handler(static function (int $severity, string $message, string $file, int $line): never {
                throw new ErrorException($message, $severity, $severity, $file, $line);
            });

            /** @var TMixed */
            return $function($this);
        } catch (Throwable $throwable) {
            throw new $class($throwable->getMessage(), $throwable->getCode(), $throwable);
        } finally {
            restore_error_handler();
        }
    }
}
