<?php

namespace Codememory\HttpFoundation\Client\Session\Storages;

use Codememory\Components\Configuration\Exceptions\NotOpenConfigException;
use Codememory\FileSystem\File;
use Codememory\HttpFoundation\Client\Session\Utils;
use Codememory\HttpFoundation\Interfaces\SessionStorageHandlerInterface;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;

/**
 * Class FileStorage
 * @package Codememory\HttpFoundation\Client\Session\Storages
 *
 * @author  Codememory
 */
class FileStorage implements SessionStorageHandlerInterface
{

    /**
     * @var ?Utils
     */
    private ?Utils $utils = null;

    /**
     * @inheritDoc
     */
    public function setUtils(Utils $utils): void
    {

        $this->utils = $utils;

    }

    /**
     * @inheritDoc
     * @throws NotOpenConfigException
     */
    public function storage(): SessionStorageInterface
    {

        $filesystem = new File();

        $nativeFileSessionHandler = new NativeFileSessionHandler($filesystem->getRealPath($this->utils->getPath()));

        return new NativeSessionStorage([], $nativeFileSessionHandler);

    }

}