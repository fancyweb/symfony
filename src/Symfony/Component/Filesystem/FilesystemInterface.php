<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Filesystem;

use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Exception\IOException;

interface FilesystemInterface
{
    /**
     * Copies a file.
     *
     * If the target file is older than the origin file, it's always overwritten.
     * If the target file is newer, it is overwritten only when the
     * $overwriteNewerFiles option is set to true.
     *
     * @throws FileNotFoundException When originFile doesn't exist
     * @throws IOException           When copy fails
     */
    public function copy(string $originFile, string $targetFile, bool $overwriteNewerFiles = false);

    /**
     * Creates a directory recursively.
     *
     * @param string|iterable $dirs The directory path
     *
     * @throws IOException On any directory creation failure
     */
    public function mkdir($dirs, int $mode = 0777);

    /**
     * Checks the existence of files or directories.
     *
     * @param string|iterable $files A filename, an array of files, or a \Traversable instance to check
     *
     * @return bool true if the file exists, false otherwise
     */
    public function exists($files);

    /**
     * Sets access and modification time of file.
     *
     * @param string|iterable $files A filename, an array of files, or a \Traversable instance to create
     * @param int|null        $time  The touch time as a Unix timestamp, if not supplied the current system time is used
     * @param int|null        $atime The access time as a Unix timestamp, if not supplied the current system time is used
     *
     * @throws IOException When touch fails
     */
    public function touch($files, int $time = null, int $atime = null);

    /**
     * Removes files or directories.
     *
     * @param string|iterable $files A filename, an array of files, or a \Traversable instance to remove
     *
     * @throws IOException When removal fails
     */
    public function remove($files);

    /**
     * Change mode for an array of files or directories.
     *
     * @param string|iterable $files     A filename, an array of files, or a \Traversable instance to change mode
     * @param int             $mode      The new mode (octal)
     * @param int             $umask     The mode mask (octal)
     * @param bool            $recursive Whether change the mod recursively or not
     *
     * @throws IOException When the change fails
     */
    public function chmod($files, int $mode, int $umask = 0000, bool $recursive = false);

    /**
     * Change the owner of an array of files or directories.
     *
     * @param string|iterable $files     A filename, an array of files, or a \Traversable instance to change owner
     * @param string|int      $user      A user name or number
     * @param bool            $recursive Whether change the owner recursively or not
     *
     * @throws IOException When the change fails
     */
    public function chown($files, $user, bool $recursive = false);

    /**
     * Change the group of an array of files or directories.
     *
     * @param string|iterable $files     A filename, an array of files, or a \Traversable instance to change group
     * @param string|int      $group     A group name or number
     * @param bool            $recursive Whether change the group recursively or not
     *
     * @throws IOException When the change fails
     */
    public function chgrp($files, $group, bool $recursive = false);

    /**
     * Renames a file or a directory.
     *
     * @throws IOException When target file or directory already exists
     * @throws IOException When origin cannot be renamed
     */
    public function rename(string $origin, string $target, bool $overwrite = false);

    /**
     * Creates a symbolic link or copy a directory.
     *
     * @throws IOException When symlink fails
     */
    public function symlink(string $originDir, string $targetDir, bool $copyOnWindows = false);

    /**
     * Creates a hard link, or several hard links to a file.
     *
     * @param string|string[] $targetFiles The target file(s)
     *
     * @throws FileNotFoundException When original file is missing or not a file
     * @throws IOException           When link fails, including if link already exists
     */
    public function hardlink(string $originFile, $targetFiles);

    /**
     * Resolves links in paths.
     *
     * With $canonicalize = false (default)
     *      - if $path does not exist or is not a link, returns null
     *      - if $path is a link, returns the next direct target of the link without considering the existence of the target
     *
     * With $canonicalize = true
     *      - if $path does not exist, returns null
     *      - if $path exists, returns its absolute fully resolved final version
     *
     * @return string|null
     */
    public function readlink(string $path, bool $canonicalize = false);

    /**
     * Given an existing path, convert it to a path relative to a given starting path.
     *
     * @return string Path of target relative to starting path
     */
    public function makePathRelative(string $endPath, string $startPath);

    /**
     * Mirrors a directory to another.
     *
     * Copies files and directories from the origin directory into the target directory. By default:
     *
     *  - existing files in the target directory will be overwritten, except if they are newer (see the `override` option)
     *  - files in the target directory that do not exist in the source directory will not be deleted (see the `delete` option)
     *
     * @param \Traversable|null $iterator Iterator that filters which files and directories to copy, if null a recursive iterator is created
     * @param array             $options  An array of boolean options
     *                                    Valid options are:
     *                                    - $options['override'] If true, target files newer than origin files are overwritten (see copy(), defaults to false)
     *                                    - $options['copy_on_windows'] Whether to copy files instead of links on Windows (see symlink(), defaults to false)
     *                                    - $options['delete'] Whether to delete files that are not in the source directory (defaults to false)
     *
     * @throws IOException When file type is unknown
     */
    public function mirror(string $originDir, string $targetDir, \Traversable $iterator = null, array $options = []);

    /**
     * Returns whether the file path is an absolute path.
     *
     * @return bool
     */
    public function isAbsolutePath(string $file);

    /**
     * Creates a temporary file with support for custom stream wrappers.
     *
     * @param string $prefix The prefix of the generated temporary filename
     *                       Note: Windows uses only the first three characters of prefix
     * @param string $suffix The suffix of the generated temporary filename
     *
     * @return string The new temporary filename (with path), or throw an exception on failure
     */
    public function tempnam(string $dir, string $prefix/*, string $suffix = ''*/);

    /**
     * Atomically dumps content into a file.
     *
     * @param string|resource $content The data to write into the file
     *
     * @throws IOException if the file cannot be written to
     */
    public function dumpFile(string $filename, $content);

    /**
     * Appends content to an existing file.
     *
     * @param string|resource $content The content to append
     *
     * @throws IOException If the file is not writable
     */
    public function appendToFile(string $filename, $content);
}
