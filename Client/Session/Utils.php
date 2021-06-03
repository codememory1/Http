<?php

namespace Codememory\HttpFoundation\Client\Session;

use Codememory\Components\Configuration\Config;
use Codememory\Components\Configuration\Exceptions\ConfigNotFoundException;
use Codememory\Components\Configuration\Exceptions\NotOpenConfigException;
use Codememory\Components\Environment\Exceptions\EnvironmentVariableNotFoundException;
use Codememory\Components\Environment\Exceptions\IncorrectPathToEnviException;
use Codememory\Components\Environment\Exceptions\ParsingErrorException;
use Codememory\Components\Environment\Exceptions\VariableParsingErrorException;
use Codememory\Components\GlobalConfig\GlobalConfig;
use Codememory\FileSystem\Interfaces\FileInterface;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;

/**
 * Class Utils
 * @package Codememory\HttpFoundation\Client\Session
 *
 * @author  Codememory
 */
class Utils
{

    public const PREFIX = '_cdm-';

    private const DEFAULT_TYPE_SAVE = 'file';
    private const DEFAULT_PATH = 'storage/sessions/';
    private const DEFAULT_NAMESPACE_HANDLER = NativeFileSessionHandler::class;

    /**
     * @var Config
     */
    private Config $config;

    /**
     * Utils constructor.
     *
     * @param FileInterface $filesystem
     *
     * @throws EnvironmentVariableNotFoundException
     * @throws IncorrectPathToEnviException
     * @throws ParsingErrorException
     * @throws VariableParsingErrorException
     * @throws ConfigNotFoundException
     */
    public function __construct(FileInterface $filesystem)
    {

        $config = new Config($filesystem);

        $this->config = $config->open(GlobalConfig::get('http.configName'));

    }

    /**
     * @return string
     * @throws NotOpenConfigException
     */
    public function getTypeSave(): string
    {

        return $this->config->get('session.typeSave') ?: self::DEFAULT_TYPE_SAVE;

    }

    /**
     * @return string
     * @throws NotOpenConfigException
     */
    public function getHandlerNamespace(): string
    {

        $key = sprintf('session.%s.handlerNamespace', $this->getTypeSave());

        return $this->config->get($key) ?: self::DEFAULT_NAMESPACE_HANDLER;

    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {

        return self::PREFIX;

    }

    /**
     * @return string
     * @throws NotOpenConfigException
     */
    public function getPath(): string
    {

        return $this->config->get('session.file.pathToSave') ?: self::DEFAULT_PATH;

    }

}