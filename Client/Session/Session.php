<?php

namespace Codememory\HttpFoundation\Client\Session;

use Codememory\Components\Configuration\Exceptions\ConfigNotFoundException;
use Codememory\Components\Configuration\Exceptions\NotOpenConfigException;
use Codememory\Components\Environment\Exceptions\EnvironmentVariableNotFoundException;
use Codememory\Components\Environment\Exceptions\IncorrectPathToEnviException;
use Codememory\Components\Environment\Exceptions\ParsingErrorException;
use Codememory\Components\Environment\Exceptions\VariableParsingErrorException;
use Codememory\FileSystem\File;
use Codememory\HttpFoundation\Interfaces\SessionStorageHandlerInterface;
use ReflectionClass;
use ReflectionException;
use Symfony\Component\HttpFoundation\Session\Session as SFSession;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\SessionStorageInterface;

/**
 * Class Session
 * @package Codememory\HttpFoundation\Client\Session
 *
 * @author  Codememory
 */
class Session
{

    /**
     * @var SFSession
     */
    private SFSession $session;

    /**
     * @var Utils
     */
    private Utils $utils;

    /**
     * @var SessionStorageInterface|null
     */
    private ?SessionStorageInterface $storage;

    /**
     * @var SessionInterface|null
     */
    private ?SessionInterface $sfSession = null;

    /**
     * Session constructor.
     * @throws ConfigNotFoundException
     * @throws EnvironmentVariableNotFoundException
     * @throws IncorrectPathToEnviException
     * @throws ParsingErrorException
     * @throws VariableParsingErrorException
     */
    public function __construct()
    {

        $this->utils = new Utils(new File());

    }

    /**
     * @return SessionInterface
     * @throws NotOpenConfigException
     * @throws ReflectionException
     */
    public function getSession(): SessionInterface
    {

        if (!$this->sfSession instanceof SessionInterface) {
            $this->sfSession = new SFSession($this->getStorage()->storage());
        }

        return $this->sfSession;

    }

    /**
     * @return SessionStorageHandlerInterface
     * @throws NotOpenConfigException
     * @throws ReflectionException
     */
    private function getStorage(): SessionStorageHandlerInterface
    {

        $reflection = new ReflectionClass($this->utils->getHandlerNamespace());
        $storage = $reflection->newInstance();

        $storage->setUtils($this->utils);

        return $storage;

    }

}