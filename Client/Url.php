<?php

namespace Codememory\Http\Client;

use Codememory\Support\ConvertType;
use Codememory\Support\Str;
use JetBrains\PhpStorm\Pure;

/**
 * Class Url
 * @package Codememory\Http\Client
 *
 * @author  Codemememory
 */
class Url
{

    /**
     * @param string $url
     *
     * @return string
     */
    #[Pure] public static function trimmingSlashes(string $url): string
    {

        return sprintf('/%s', trim($url, '/'));

    }

    /**
     * @return string
     */
    #[Pure] public static function current(): string
    {

        return self::trimmingSlashes($_SERVER['REQUEST_URI']);

    }

    /**
     * @param string $url
     *
     * @return string
     */
    public static function removeParameters(string $url): string
    {

        $startPositionParameters = mb_strripos($url, '?');

        if (self::existParameters($url)) {
            return self::trimmingSlashes(Str::cut($url, $startPositionParameters));
        }

        return self::trimmingSlashes($url);

    }

    /**
     * @param string $url
     *
     * @return bool
     */
    public static function existParameters(string $url): bool
    {

        $url = self::trimmingSlashes($url);
        $startPositionParameters = mb_strripos($url, '?');

        if ($startPositionParameters) {
            return true;
        }

        return false;

    }

    /**
     * @param string      $url
     * @param bool        $toString
     * @param string|null $charToBegin
     *
     * @return string|array|null
     */
    public static function getParameters(string $url, bool $toString = false, ?string $charToBegin = null): null|string|array
    {

        $convertType = new ConvertType();
        $url = rtrim($url, '&');
        $parametersToString = null;
        $parametersToArray = [];

        if (self::existParameters($url)) {
            $parametersToString = mb_substr($url, self::getStartPositionParameters($url) + 1);
            $parameters = explode('&', $parametersToString);

            foreach ($parameters as $parameter) {
                [$name, $value] = explode('=', $parameter);

                $parametersToArray[$name] = $convertType->auto($value);
            }

        }

        if ($toString) {
            return null !== $parametersToString ? $charToBegin . $parametersToString : null;
        }

        return $parametersToArray;

    }

    /**
     * @param string $url
     * @param array  $parameters
     *
     * @return string
     */
    public static function addParameters(string $url, array $parameters): string
    {

        $parameters = array_merge(self::getParameters($url), $parameters);
        $parametersToString = '?';

        foreach ($parameters as $name => $value) {
            $parametersToString .= sprintf('%s=%s&', $name, $value);
        }

        $parametersToString = mb_substr($parametersToString, 0, -1);

        return self::removeParameters($url) . $parametersToString;

    }

    /**
     * @param string $url
     * @param string $path
     *
     * @return string
     */
    public static function addPath(string $url, string $path): string
    {

        $path = self::trimmingSlashes($path);
        $urlWithoutParameters = self::removeParameters($url);
        $parameters = self::getParameters($url, true, '?');
        $gluedUrl = $urlWithoutParameters . $path;

        if (null !== $parameters) {
            $gluedUrl .= $parameters;
        }

        return $gluedUrl;

    }


    /**
     * @param string|null $url
     *
     * @return string
     */
    #[Pure] public function getAutoUrl(?string $url = null): string
    {

        return null === $url ? self::current() : self::trimmingSlashes($url);

    }

    /**
     * @param string $url
     *
     * @return string|null
     */
    public static function removeExtension(string $url): ?string
    {

        $urlWithoutParameters = self::removeParameters($url);
        $parameters = self::getParameters($url, true, '?');
        $startPositionExtension = mb_strripos($urlWithoutParameters, '.');
        $fullUrl = $urlWithoutParameters;

        if ($startPositionExtension) {
            $fullUrl = Str::cut($urlWithoutParameters, $startPositionExtension);
        }

        return null !== $parameters ? $fullUrl . $parameters : $fullUrl;

    }

    /**
     * @return string
     */
    #[Pure] public static function getHostIp(): string
    {

        return gethostbyname(self::getHost());

    }

    /**
     * @param string $host
     *
     * @return string
     */
    #[Pure] public static function hostWithSchema(string $host): string
    {

        return self::getScheme().$host;

    }

    /**
     * @return string
     */
    public static function getHost(): string
    {

        return $_SERVER['HTTP_HOST'];

    }

    /**
     * @return string
     */
    #[Pure] public static function getHostDomainName(): string
    {

        return gethostbyaddr(self::getHostIp());

    }

    /**
     * @param mixed|null $schema
     *
     * @return string|null
     */
    #[Pure] public static function getScheme(mixed $schema = null): ?string
    {

        if (null !== $schema) {
            return sprintf('%s://', $schema);
        }

        if((int) $_SERVER['SERVER_PORT'] === 443
            || ($_SERVER['HTTPS'] ?? null && 'off' !== Str::toLowercase($_SERVER['HTTPS']))
            || 'https' === $_SERVER['REQUEST_SCHEME'] ?? null) {
            return 'https://';
        }

        return 'http://';

    }

    /**
     * @param string $url
     *
     * @return string
     */
    #[Pure] public static function getFullUrl(string $url): string
    {

        return sprintf('%s%s%s', self::getScheme(), self::getHost(), self::trimmingSlashes($url));

    }

    /**
     * @param string $url
     *
     * @return false|int
     */
    private static function getStartPositionParameters(string $url): bool|int
    {

        $url = self::trimmingSlashes($url);

        return mb_strripos($url, '?');

    }

}