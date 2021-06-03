<?php

namespace Codememory\HttpFoundation\ControlHttpStatus;

use Closure;
use Codememory\Components\Configuration\Exceptions\NotOpenConfigException;
use Codememory\Components\View\Engines\BigEngine;
use Codememory\Components\View\Engines\TwigEngine;
use Codememory\HttpFoundation\Response\Response;
use Codememory\HttpFoundation\Traits\ResponseCodeControlPriorityTrait;

/**
 * Class ControlResponseCode
 * @package Codememory\HttpFoundation\ControlResponseCode
 *
 * @author  Codememory
 */
class ControlResponseCode
{

    use ResponseCodeControlPriorityTrait;

    /**
     * @var array|string[]
     */
    private array $priorityMethods = [
        'view'  => 'viewPriority',
        'class' => 'classPriority'
    ];

    /**
     * @var array|string[]
     */
    private array $engines = [
        'big'  => BigEngine::class,
        'twig' => TwigEngine::class
    ];

    /**
     * @var Response
     */
    private Response $response;

    /**
     * @var Utils
     */
    private Utils $utils;

    /**
     * ControlResponseCode constructor.
     */
    public function __construct(Response $response)
    {

        $this->response = $response;
        $this->utils = new Utils();

    }

    /**
     * @throws NotOpenConfigException
     */
    public function trackResponseStatus(): void
    {

        if ($this->existResponseCodeInConfiguration()) {
            $responseCodeData = $this->getResponseCodeData();

            $responseCodeData['engine'] = $this->engines[$responseCodeData['engine']];

            $this->callPriorityMethod($responseCodeData['priority'], $responseCodeData)->__invoke();
        }

    }

    /**
     * @param string $priority
     * @param array  $responseCodeData
     *
     * @return Closure
     */
    private function callPriorityMethod(string $priority, array $responseCodeData): Closure
    {

        $methodName = $this->priorityMethods[$priority] ?? Utils::DEFAULT_PRIORITY;

        return call_user_func([$this, $methodName], $responseCodeData);

    }

    /**
     * @return array
     * @throws NotOpenConfigException
     */
    private function getResponseCodeData(): array
    {

        return $this->utils->getHttpStatusData($this->getResponseCode());

    }

    /**
     * @return bool
     * @throws NotOpenConfigException
     */
    private function existResponseCodeInConfiguration(): bool
    {

        $code = $this->getResponseCode();
        $codesInConfiguration = $this->utils->getHttpCodes();

        return in_array($code, $codesInConfiguration);

    }

    /**
     * @return int
     */
    private function getResponseCode(): int
    {

        return $this->response->getResponseCode();

    }

}