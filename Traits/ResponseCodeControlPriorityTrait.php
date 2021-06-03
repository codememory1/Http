<?php

namespace Codememory\HttpFoundation\Traits;

use Closure;
use Codememory\Components\Configuration\Exceptions\ConfigNotFoundException;
use Codememory\Components\Environment\Exceptions\EnvironmentVariableNotFoundException;
use Codememory\Components\Environment\Exceptions\IncorrectPathToEnviException;
use Codememory\Components\Environment\Exceptions\ParsingErrorException;
use Codememory\Components\Environment\Exceptions\VariableParsingErrorException;
use Codememory\Components\View\View;
use Codememory\FileSystem\File;
use Codememory\Support\Arr;

/**
 * Trait ResponseCodeControlPriorityTrait
 * @package Codememory\HttpFoundation\Traits
 *
 * @author  Codememory
 */
trait ResponseCodeControlPriorityTrait
{

    /**
     * @param array $responseCodeData
     *
     * @return Closure
     */
    private function classPriority(array $responseCodeData): Closure
    {

        $data = Arr::set($responseCodeData)::select('class', 'method');

        return function () use ($data) {
            return call_user_func([$data['class'], $data['method']]);
        };

    }

    /**
     * @param array $responseCodeData
     *
     * @return Closure
     * @throws ConfigNotFoundException
     * @throws EnvironmentVariableNotFoundException
     * @throws IncorrectPathToEnviException
     * @throws ParsingErrorException
     * @throws VariableParsingErrorException
     */
    private function viewPriority(array $responseCodeData): Closure
    {

        $view = new View(new File());
        $data = Arr::set($responseCodeData)::select('engine', 'view');

        return $view->engine(new $data['engine']())->render($data['view'])->getTemplateClosure();

    }

}