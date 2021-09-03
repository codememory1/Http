<?php

namespace Codememory\HttpFoundation\ControlHttpStatus;

use Codememory\Components\Configuration\Config;
use Codememory\Components\Configuration\Configuration;
use Codememory\Components\GlobalConfig\GlobalConfig;
use JetBrains\PhpStorm\ArrayShape;

/**
 * Class Utils
 * @package Codememory\HttpFoundation\ControlResponseCode
 *
 * @author  Codememory
 */
class Utils
{

    public const DEFAULT_PRIORITY = 'view';
    private const PRIORITY_LIST = ['class', 'view'];
    private const ENGINE_LIST = ['big', 'twig'];
    private const DEFAULT_ENGINE = 'big';

    /**
     * @var Config
     */
    private Config $config;

    /**
     * Utils constructor.
     */
    public function __construct()
    {

        $this->config = Configuration::getInstance()->open(GlobalConfig::get('controlHttpStatus.configName'));

    }

    /**
     * @return array
     */
    #[ArrayShape(['priority' => "\null|string", 'engine' => "\null|string", 'class' => "\null|string"])]
    public function getGeneral(): array
    {

        $general = $this->config->get('_general') ?: [];

        return $this->getStructure(
            $general['priority'] ?? self::DEFAULT_PRIORITY,
            $general['engine'] ?? self::DEFAULT_ENGINE,
            $general['class'] ?? null
        );

    }

    /**
     * @return array
     */
    public function getHttpCodes(): array
    {

        $keys = array_keys($this->config->all());

        foreach ($keys as $index => $value) {
            if (!is_int($value)) {
                unset($keys[$index]);
            }
        }

        return $keys;

    }

    /**
     * @param int $code
     *
     * @return array
     */
    public function getHttpStatusData(int $code): array
    {

        $data = $this->config->get((string) $code);
        $general = $this->getGeneral();
        $readyStructure = $this->getStructure(
            $data['priority'] ?? $general['priority'],
            $data['engine'] ?? $general['engine'],
            $data['class'] ?? $general['class'],
        );

        return array_merge($readyStructure, [
            'method' => $data['method'] ?? null,
            'view'   => $data['view'] ?? null
        ]);

    }

    /**
     * @param string|null $priority
     * @param string|null $engine
     * @param string|null $class
     *
     * @return array
     */
    #[ArrayShape(['priority' => "null|string", 'engine' => "null|string", 'class' => "null|string"])]
    public function getStructure(?string $priority, ?string $engine, ?string $class): array
    {

        return [
            'priority' => $priority,
            'engine'   => $engine,
            'class'    => $class,
        ];

    }

}