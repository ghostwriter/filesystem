<?php

declare(strict_types=1);

namespace Tests\Unit;

use Ghostwriter\Filesystem\Filesystem;
use Ghostwriter\Filesystem\Interface\PathInterface;
use Ghostwriter\Filesystem\Path;
use PHPUnit\Framework\Attributes\CoversClass;
use SplFileInfo;
use Throwable;

use const DIRECTORY_SEPARATOR;

#[CoversClass(Filesystem::class)]
#[CoversClass(Path::class)]
final class FilesystemTest extends AbstractTestCase
{
    /**
     * @throws Throwable
     */
    public function testAppend(): void
    {
        $filesystem = Filesystem::new();

        $file = self::$temporaryDirectory . 'file.txt';

        $prefix = 'Hello, ';

        $suffix = 'world!';

        $contents = $prefix . $suffix;

        $filesystem->write($file, $prefix);

        self::assertStringEqualsFile($file, $prefix);

        $bytes = $filesystem->append($file, $suffix);

        self::assertSame(\mb_strlen($contents), $bytes);

        self::assertStringEqualsFile($file, $contents);

        self::assertSame($contents, $filesystem->read($file));
    }

    /**
     * @throws Throwable
     */
    public function testBasename(): void
    {
        $filesystem = Filesystem::new();

        $path = self::$temporaryDirectory . 'file.txt';

        self::assertSame('file.txt', $filesystem->basename($path));

        self::assertSame('file', $filesystem->basename($path, '.txt'));
    }

    /**
     * @throws Throwable
     */
    public function testChmod(): void
    {
        $filesystem = Filesystem::new();

        $file = self::$temporaryDirectory . 'file.txt';

        $contents = 'Hello, world!';

        $filesystem->write($file, $contents);

        self::assertFileExists($file);

        self::assertSame('644', $filesystem->permissions($file));
        self::assertSame('644', \sprintf('%o', \fileperms($file) & 0o777));

        $filesystem->chmod($file, 0o400);

        self::assertSame('400', $filesystem->permissions($file));
        self::assertSame('400', \sprintf('%o', \fileperms($file) & 0o777));

    }

    /**
     * @throws Throwable
     */
    public function testCleanDirectory(): void
    {
        $filesystem = Filesystem::new();

        $directory = self::$temporaryDirectory . 'directory';

        $file = $directory . DIRECTORY_SEPARATOR . 'file.txt';

        $contents = 'Hello, world!';

        $filesystem->write($file, $contents);

        self::assertFileExists($file);

        $filesystem->cleanDirectory($directory);

        self::assertDirectoryExists($directory);

        self::assertEmpty(\iterator_to_array($filesystem->filesystemIterator($directory)));
    }

    /**
     * @throws Throwable
     */
    public function testCopy(): void
    {
        $filesystem = Filesystem::new();

        $source = self::$temporaryDirectory . 'source.txt';

        $target = self::$temporaryDirectory . 'target.txt';

        $contents = 'Hello, world!';

        $filesystem->write($source, $contents);

        self::assertFileExists($source);

        self::assertFileDoesNotExist($target);

        $filesystem->copy($source, $target);

        self::assertFileExists($target);

        self::assertStringEqualsFile($target, $contents);
    }

    /**
     * @throws Throwable
     */
    public function testCreateDirectory(): void
    {
        $filesystem = Filesystem::new();

        $directory = self::$temporaryDirectory . 'directory';

        self::assertDirectoryDoesNotExist($directory);

        $filesystem->createDirectory($directory);

        self::assertDirectoryExists($directory);
    }

    /**
     * @throws Throwable
     */
    public function testCreateFile(): void
    {
        $filesystem = Filesystem::new();

        $file = self::$temporaryDirectory . 'file.txt';

        self::assertFileDoesNotExist($file);

        $filesystem->createFile($file);

        self::assertFileExists($file);

        self::assertEmpty($filesystem->read($file));
    }

    /**
     * @throws Throwable
     */
    public function testCreateTemporaryDirectory(): void
    {
        $filesystem = Filesystem::new();

        $directory = $filesystem->createTemporaryDirectory('prefix');

        self::assertDirectoryExists($directory);
    }

    /**
     * @throws Throwable
     */
    public function testCreateTemporaryFile(): void
    {
        $filesystem = Filesystem::new();

        $file = $filesystem->createTemporaryFile('prefix');

        self::assertFileExists($file);

        self::assertEmpty($filesystem->read($file));
    }

    /**
     * @throws Throwable
     */
    public function testCurrentWorkingDirectory(): void
    {
        self::assertSame(\getcwd(), Filesystem::new()->currentWorkingDirectory());
    }

    /**
     * @throws Throwable
     */
    public function testDelete(): void
    {
        $filesystem = Filesystem::new();

        $file = self::$temporaryDirectory . 'file.txt';

        $contents = 'Hello, world!';

        $filesystem->write($file, $contents);

        self::assertFileExists($file);

        $filesystem->delete($file);

        self::assertFileDoesNotExist($file);
    }

    /**
     * @throws Throwable
     */
    public function testDeleteDirectory(): void
    {
        $filesystem = Filesystem::new();

        $directory = self::$temporaryDirectory . 'directory';

        $file = $directory . DIRECTORY_SEPARATOR . 'file.txt';

        $contents = 'Hello, world!';

        $filesystem->write($file, $contents);

        self::assertFileExists($file);

        $filesystem->deleteDirectory($directory);

        self::assertDirectoryDoesNotExist($directory);
    }

    /**
     * @throws Throwable
     */
    public function testDeleteFile(): void
    {
        $filesystem = Filesystem::new();

        $file = self::$temporaryDirectory . 'file.txt';

        $contents = 'Hello, world!';

        $filesystem->write($file, $contents);

        self::assertFileExists($file);

        $filesystem->deleteFile($file);

        self::assertFileDoesNotExist($file);
    }

    /**
     * @throws Throwable
     */
    public function testDeleteLink(): void
    {
        $filesystem = Filesystem::new();

        $target = self::$temporaryDirectory . 'target.txt';

        self::assertFileDoesNotExist($target);

        $link = self::$temporaryDirectory . 'link.txt';

        self::assertFileDoesNotExist($link);

        $contents = 'Hello, world!';

        $filesystem->write($target, $contents);

        self::assertFileExists($target);

        self::assertFileDoesNotExist($link);

        $filesystem->symlink($target, $link);

        self::assertFileExists($link);

        self::assertSame($contents, $filesystem->read($link));

        $filesystem->deleteLink($link);

        self::assertFileDoesNotExist($link);
    }

    /**
     * @throws Throwable
     */
    public function testExists(): void
    {
        $filesystem = Filesystem::new();

        $file = self::$temporaryDirectory . 'file.txt';

        self::assertFileDoesNotExist($file);

        $filesystem->write($file, 'Hello, world!');

        self::assertFileExists($file);

        self::assertTrue($filesystem->exists($file));
    }

    /**
     * @throws Throwable
     */
    public function testExtension(): void
    {
        $filesystem = Filesystem::new();

        $path = self::$temporaryDirectory . 'file.txt';

        self::assertSame('txt', $filesystem->extension($path));
    }

    /**
     * @throws Throwable
     */
    public function testFilename(): void
    {
        $filesystem = Filesystem::new();

        $path = self::$temporaryDirectory . 'file.txt';

        self::assertSame('file', $filesystem->filename($path));
    }

    /**
     * @throws Throwable
     */
    public function testFilesystemIterator(): void
    {
        $filesystem = Filesystem::new();

        $directory = self::$temporaryDirectory . 'directory';

        $file = $directory . DIRECTORY_SEPARATOR . 'file.txt';

        $contents = 'Hello, world!';

        $filesystem->write($file, $contents);

        self::assertFileExists($file);

        $iterator = $filesystem->filesystemIterator($directory);

        self::assertSame($file, $iterator->current()->getPathname());
    }

    /**
     * @throws Throwable
     */
    public function testIsDirectory(): void
    {
        $filesystem = Filesystem::new();

        $directory = self::$temporaryDirectory . 'directory';

        $file = $directory . DIRECTORY_SEPARATOR . 'file.txt';

        $contents = 'Hello, world!';

        $filesystem->write($file, $contents);

        self::assertFileExists($file);

        self::assertTrue($filesystem->isDirectory($directory));
        self::assertFalse($filesystem->isDirectory($file));
    }

    /**
     * @throws Throwable
     */
    public function testIsExecutable(): void
    {
        $filesystem = Filesystem::new();

        $file = self::$temporaryDirectory . 'file.txt';

        $contents = 'Hello, world!';

        $filesystem->write($file, $contents);

        self::assertFileExists($file);

        self::assertFalse($filesystem->isExecutable($file));
    }

    /**
     * @throws Throwable
     */
    public function testIsFile(): void
    {
        $filesystem = Filesystem::new();

        $directory = self::$temporaryDirectory . 'directory';

        $file = $directory . DIRECTORY_SEPARATOR . 'file.txt';

        $contents = 'Hello, world!';

        $filesystem->write($file, $contents);

        self::assertFileExists($file);

        self::assertFalse($filesystem->isFile($directory));
        self::assertTrue($filesystem->isFile($file));
    }

    /**
     * @throws Throwable
     */
    public function testIsLink(): void
    {
        $filesystem = Filesystem::new();

        $target = self::$temporaryDirectory . 'target.txt';

        self::assertFileDoesNotExist($target);

        $link = self::$temporaryDirectory . 'link.txt';

        self::assertFileDoesNotExist($link);

        $contents = 'Hello, world!';

        $filesystem->write($target, $contents);

        self::assertFileExists($target);

        self::assertFileDoesNotExist($link);

        $filesystem->symlink($target, $link);

        self::assertFileExists($link);

        self::assertTrue($filesystem->isLink($link));
        self::assertFalse($filesystem->isLink($target));
    }

    /**
     * @throws Throwable
     */
    public function testIsReadable(): void
    {
        $filesystem = Filesystem::new();

        $file = self::$temporaryDirectory . 'file.txt';

        $contents = 'Hello, world!';

        $filesystem->write($file, $contents);

        self::assertFileExists($file);

        self::assertTrue($filesystem->isReadable($file));
    }

    /**
     * @throws Throwable
     */
    public function testLastAccessTime(): void
    {
        $filesystem = Filesystem::new();

        $file = self::$temporaryDirectory . 'file.txt';

        $contents = 'Hello, world!';

        $filesystem->write($file, $contents);

        self::assertFileExists($file);

        self::assertLessThanOrEqual(\time(), $filesystem->lastAccessTime($file));
    }

    /**
     * @throws Throwable
     */
    public function testLastChangeTime(): void
    {
        $filesystem = Filesystem::new();

        $file = self::$temporaryDirectory . 'file.txt';

        $contents = 'Hello, world!';

        $filesystem->write($file, $contents);

        self::assertFileExists($file);

        self::assertLessThanOrEqual(\time(), $filesystem->lastChangeTime($file));
    }

    /**
     * @throws Throwable
     */
    public function testLastModifiedTime(): void
    {
        $filesystem = Filesystem::new();

        $file = self::$temporaryDirectory . 'file.txt';

        $contents = 'Hello, world!';

        $filesystem->write($file, $contents);

        self::assertFileExists($file);

        self::assertLessThanOrEqual(\time(), $filesystem->lastModifiedTime($file));
    }

    /**
     * @throws Throwable
     */
    public function testLinkTarget(): void
    {
        $filesystem = Filesystem::new();

        $target = self::$temporaryDirectory . 'target.txt';

        self::assertFileDoesNotExist($target);

        $link = self::$temporaryDirectory . 'link.txt';

        self::assertFileDoesNotExist($link);

        $contents = 'Hello, world!';

        $filesystem->write($target, $contents);

        self::assertFileExists($target);

        self::assertFileDoesNotExist($link);

        $filesystem->symlink($target, $link);

        self::assertFileExists($link);

        self::assertSame($target, $filesystem->linkTarget($link));
    }

    /**
     * @throws Throwable
     */
    public function testListDirectory(): void
    {
        $filesystem = Filesystem::new();

        $directory = self::$temporaryDirectory . 'directory';

        $expected = $directory . DIRECTORY_SEPARATOR . 'file.txt';

        $contents = 'Hello, world!';

        $filesystem->write($expected, $contents);

        self::assertFileExists($expected);

        foreach ($filesystem->listDirectory($directory) as $file) {
            self::assertInstanceOf(SplFileInfo::class, $file);
            self::assertSame($expected, $file->getPathname());
            return;
        }

        self::fail('Should have found a file.');
    }

    /**
     * @throws Throwable
     */
    public function testMissing(): void
    {
        $filesystem = Filesystem::new();

        $file = self::$temporaryDirectory . 'file.txt';

        self::assertFileDoesNotExist($file);

        self::assertTrue($filesystem->missing($file));
    }

    /**
     * @throws Throwable
     */
    public function testMove(): void
    {
        $filesystem = Filesystem::new();

        $source = self::$temporaryDirectory . 'source.txt';

        $target = self::$temporaryDirectory . 'target.txt';

        $contents = 'Hello, world!';

        $filesystem->write($source, $contents);

        self::assertFileExists($source);

        self::assertFileDoesNotExist($target);

        $filesystem->move($source, $target);

        self::assertFileDoesNotExist($source);

        self::assertFileExists($target);

        self::assertStringEqualsFile($target, $contents);
    }

    /**
     * @throws Throwable
     */
    public function testParentDirectory(): void
    {
        $filesystem = Filesystem::new();

        $path = self::$temporaryDirectory . 'directory/file.txt';

        self::assertSame(self::$temporaryDirectory . 'directory', $filesystem->parentDirectory($path));
    }

    /**
     * @throws Throwable
     */
    public function testPathname(): void
    {
        $filesystem = Filesystem::new();

        $path = self::$temporaryDirectory . 'file.txt';

        self::assertSame($path, $filesystem->pathname($path));
    }

    /**
     * @throws Throwable
     */
    public function testPrepend(): void
    {
        $filesystem = Filesystem::new();

        $file = self::$temporaryDirectory . 'file.txt';

        $prefix = 'Hello, ';

        $suffix = 'world!';

        $contents = $prefix . $suffix;

        $filesystem->write($file, $suffix);

        self::assertStringEqualsFile($file, $suffix);

        $bytes = $filesystem->prepend($file, $prefix);

        self::assertSame(\mb_strlen($contents), $bytes);

        self::assertStringEqualsFile($file, $contents);

        self::assertSame($contents, $filesystem->read($file));
    }

    /**
     * @throws Throwable
     */
    public function testRead(): void
    {
        $filesystem = Filesystem::new();

        $file = self::$temporaryDirectory . 'file.txt';

        $contents = 'Hello, world!';

        $filesystem->write($file, $contents);

        self::assertStringEqualsFile($file, $contents);

        self::assertSame($contents, $filesystem->read($file));
    }

    /**
     * @throws Throwable
     */
    public function testRealpath(): void
    {
        $filesystem = Filesystem::new();

        $file = self::$temporaryDirectory . 'file.txt';

        $contents = '#BlackLivesMatter';

        $filesystem->write($file, $contents);

        self::assertStringEqualsFile($file, $contents);

        self::assertSame($file, $filesystem->realpath($file));
    }

    /**
     * @throws Throwable
     */
    public function testRecursiveDirectoryIterator(): void
    {
        $filesystem = Filesystem::new();

        $directory = self::$temporaryDirectory . 'directory';

        $file = $directory . DIRECTORY_SEPARATOR . 'file.txt';

        $contents = 'Hello, world!';

        $filesystem->write($file, $contents);

        foreach ($filesystem->recursiveDirectoryIterator($directory) as $fileInfo) {
            self::assertInstanceOf(SplFileInfo::class, $fileInfo);

            self::assertSame($file, $fileInfo->getPathname());
            return;
        }

        self::fail('Should have found a file.');
    }

    /**
     * @throws Throwable
     */
    public function testRecursiveIterator(): void
    {
        $filesystem = Filesystem::new();

        $directory = self::$temporaryDirectory . 'directory';

        $expected = $directory . DIRECTORY_SEPARATOR . 'file.txt';

        $contents = 'Hello, world!';

        $filesystem->write($expected, $contents);

        foreach ($filesystem->recursiveIterator($directory) as $file) {
            self::assertInstanceOf(PathInterface::class, $file);
            self::assertInstanceOf(Path::class, $file);
            self::assertSame($expected, $file->toString());

            return;
        }

        self::fail('Should have found a file.');
    }

    /**
     * @throws Throwable
     */
    public function testRecursiveRegexIterator(): void
    {
        $filesystem = Filesystem::new();

        $directory = self::$temporaryDirectory . 'directory';

        $expected = $directory . DIRECTORY_SEPARATOR . 'file.txt';

        $contents = 'Hello, world!';

        $filesystem->write($expected, $contents);

        foreach (
            $filesystem->recursiveRegexIterator(
                $filesystem->recursiveDirectoryIterator($directory),
                '#\.txt$#iu',
            ) as $file => $fileInfo
        ) {
            self::assertSame($expected, $file);
            self::assertInstanceOf(SplFileInfo::class, $fileInfo);

            return;
        }

        self::fail('Should have found a file.');
    }

    /**
     * @throws Throwable
     */
    public function testRelative(): void
    {
        $filesystem = Filesystem::new();

        $from = self::$temporaryDirectory . 'from';

        $to = self::$temporaryDirectory . 'to';

        $filesystem->createDirectory($from);

        $filesystem->createDirectory($to);

        self::assertSame('../from/', $filesystem->relative($to, $from));
    }

    /**
     * @throws Throwable
     */
    public function testSize(): void
    {
        $filesystem = Filesystem::new();

        $file = self::$temporaryDirectory . 'file.txt';

        $contents = 'Hello, world!';

        $filesystem->write($file, $contents);

        self::assertStringEqualsFile($file, $contents);

        self::assertSame(\mb_strlen($contents), $filesystem->size($file));
    }

    /**
     * @throws Throwable
     */
    public function testSymlink(): void
    {
        $filesystem = Filesystem::new();

        $target = self::$temporaryDirectory . 'target.txt';

        self::assertFileDoesNotExist($target);

        $link = self::$temporaryDirectory . 'link.txt';

        self::assertFileDoesNotExist($link);

        $contents = 'Hello, world!';

        $filesystem->write($target, $contents);

        self::assertFileExists($target);

        self::assertFileDoesNotExist($link);

        $filesystem->symlink($target, $link);

        self::assertFileExists($link);

        self::assertSame($contents, $filesystem->read($link));
    }

    /**
     * @throws Throwable
     */
    public function testTemporaryDirectory(): void
    {
        self::assertSame(Filesystem::new()->temporaryDirectory(), \sys_get_temp_dir());
    }

    /**
     * @throws Throwable
     */
    public function testWrite(): void
    {
        $filesystem = Filesystem::new();

        $file = self::$temporaryDirectory . 'file.txt';

        $contents = 'Hello, world!';

        $filesystem->write($file, $contents);

        self::assertFileExists($file);

        self::assertStringEqualsFile($file, $contents);
    }
}
