<?php

namespace Codememory\Http\Client\Header;

/**
 * Class Parser
 * @package System\Http\Response\Headers
 *
 * @author  Codememory
 */
class Parser
{

    private const ENUM_SIGN = ',';
    private const LIST_SIGN = ';';

    /**
     * @param string      $sign
     * @param string|null $string
     *
     * @return array
     */
    private function commonHandler(string $sign, ?string $string = null): array
    {

        return array_map(fn (string $value) => trim($value), explode($sign, $string));

    }

    /**
     * @param string|null $string
     *
     * @return array
     */
    public function directives(?string $string = null): array
    {

        return $this->commonHandler(self::ENUM_SIGN, $string);

    }

    /**
     * @param string|null $string
     *
     * @return array
     */
    public function factor(?string $string = null): array
    {

        return $this->commonHandler(self::LIST_SIGN, $string);

    }

}