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

use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Retry\GenericRetryStrategy;
use Symfony\Component\Filesystem\Retry\RetryStrategyInterface;

class RetryableFilesystem implements FilesystemInterface
{
    private $filesystem;
    private $strategy;
    private $maxRetries;

    public function __construct(Filesystem $filesystem = null, RetryStrategyInterface $strategy = null, int $maxRetries = 3)
    {
        $this->filesystem = $filesystem ?? new Filesystem();
        $this->strategy = $strategy ?? new GenericRetryStrategy();
        $this->maxRetries = $maxRetries;
    }

    /**
     * {@inheritdoc}
     */
    public function copy(string $originFile, string $targetFile, bool $overwriteNewerFiles = false)
    {
        $this->retry(__FUNCTION__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function mkdir($dirs, int $mode = 0777)
    {
        $this->retry(__FUNCTION__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function exists($files)
    {
        return $this->filesystem->exists(...\func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function touch($files, int $time = null, int $atime = null)
    {
        $this->retry(__FUNCTION__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function remove($files)
    {
        $this->retry(__FUNCTION__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function chmod($files, int $mode, int $umask = 0000, bool $recursive = false)
    {
        $this->retry(__FUNCTION__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function chown($files, $user, bool $recursive = false)
    {
        $this->retry(__FUNCTION__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function chgrp($files, $group, bool $recursive = false)
    {
        $this->retry(__FUNCTION__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function rename(string $origin, string $target, bool $overwrite = false)
    {
        $this->retry(__FUNCTION__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function symlink(string $originDir, string $targetDir, bool $copyOnWindows = false)
    {
        $this->retry(__FUNCTION__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function hardlink(string $originFile, $targetFiles)
    {
        $this->retry(__FUNCTION__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function readlink(string $path, bool $canonicalize = false)
    {
        return $this->filesystem->readlink(...\func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function makePathRelative(string $endPath, string $startPath)
    {
        return $this->filesystem->makePathRelative(...\func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function mirror(string $originDir, string $targetDir, \Traversable $iterator = null, array $options = [])
    {
        $this->retry(__FUNCTION__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function isAbsolutePath(string $file)
    {
        return $this->filesystem->isAbsolutePath(...\func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function tempnam(string $dir, string $prefix/*, string $suffix = ''*/)
    {
        return $this->retry(__FUNCTION__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function dumpFile(string $filename, $content)
    {
        $this->retry(__FUNCTION__, \func_get_args());
    }

    /**
     * {@inheritdoc}
     */
    public function appendToFile(string $filename, $content)
    {
        $this->retry(__FUNCTION__, \func_get_args());
    }

    private function retry(string $method, array $args)
    {
        $retryCount = 0;
        $i = 0;
        while (true) {
            try {
                return $this->filesystem->$method(...$args);
            } catch (IOExceptionInterface $e) {
                if ($retryCount >= $this->maxRetries) {
                    throw $e;
                }

                usleep($this->strategy->getDelay($retryCount++) * 1000);
            }
        }
    }
}
