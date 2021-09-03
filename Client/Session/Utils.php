<?php

namespace Codememory\HttpFoundation\Client\Session;

use Codememory\Components\Configuration\Config;
use Codememory\Components\Configuration\Configuration;
use Codememory\Components\GlobalConfig\GlobalConfig;
use Codememory\HttpFoundation\Client\Session\Storages\FileStorage;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeFileSessionHandler;

/**
 * Class Utils
 *
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
     */
    public function __construct()
    {

        $this->config = Configuration::getInstance()->open(GlobalConfig::get('http.configName'), $this->defaultConfig());

    }

    /**
     * @return string
     */
    public function getTypeSave(): string
    {

        return $this->config->get('session.typeSave') ?: self::DEFAULT_TYPE_SAVE;

    }

    /**
     * @return string
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
     */
    public function getPath(): string
    {

        return $this->config->get('session.file.pathToSave') ?: self::DEFAULT_PATH;

    }

    /**
     * @return array[]
     */
    #[ArrayShape(['session' => "array"])]
    private function defaultConfig(): array
    {

        return [
            'session' => [
                'typeSave' => 'file',
                'file'     => [
                    'handlerNamespace' => FileStorage::class,
                    'pathSave'         => 'storage/sessions/'
                ]
            ]
        ];

    }

}