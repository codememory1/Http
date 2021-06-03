<?php

namespace Codememory\HttpFoundation\Client;

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
    #[Pure] public function trimmingSlashes(string $url): string
    {

        return sprintf('/%s', trim($url, '/'));

    }

    /**
     * @return string
     */
    #[Pure] public function current(): string
    {

        return $this->trimmingSlashes($_SERVER['REQUEST_URI']);

    }

    /**
     * @param string $url
     *
     * @return string
     */
    public function removeParameters(string $url): string
    {

        $startPositionParameters = mb_strripos($url, '?');

        if ($this->existParameters($url)) {
            return $this->trimmingSlashes(Str::cut($url, $startPositionParameters));
        }

        return $this->trimmingSlashes($url);

    }

    /**
     * @param string $url
     *
     * @return bool
     */
    public function existParameters(string $url): bool
    {

        $url = $this->trimmingSlashes($url);
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
    public function getParameters(string $url, bool $toString = false, ?string $charToBegin = null): null|string|array
    {

        $convertType = new ConvertType();
        $url = rtrim($url, '&');
        $parametersToString = null;
        $parametersToArray = [];

        if ($this->existParameters($url)) {
            $parametersToString = mb_substr($url, $this->getStartPositionParameters($url) + 1);
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
    public function addParameters(string $url, array $parameters): string
    {

        $parameters = array_merge($this->getParameters($url), $parameters);
        $parametersToString = '?';

        foreach ($parameters as $name => $value) {
            $parametersToString .= sprintf('%s=%s&', $name, $value);
        }

        $parametersToString = mb_substr($parametersToString, 0, -1);

        return $this->removeParameters($url) . $parametersToString;

    }

    /**
     * @param string $url
     * @param string $path
     *
     * @return string
     */
    public function addPath(string $url, string $path): string
    {

        $path = $this->trimmingSlashes($path);
        $urlWithoutParameters = $this->removeParameters($url);
        $parameters = $this->getParameters($url, true, '?');
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
    #[Pure] public function getUrl(?string $url = null): string
    {

        return null === $url ? $this->current() : $this->trimmingSlashes($url);

    }

    /**
     * @param string $url
     *
     * @return string|null
     */
    public function removeExtension(string $url): ?string
    {

        $urlWithoutParameters = $this->removeParameters($url);
        $parameters = $this->getParameters($url, true, '?');
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
    #[Pure] public function getHostIp(): string
    {

        return gethostbyname($this->getHost());

    }

    /**
     * @param string $host
     *
     * @return string
     */
    #[Pure] public function hostWithSchema(string $host): string
    {

        return $this->getScheme() . $host;

    }

    /**
     * @return string
     */
    public function getHost(): string
    {

        return $_SERVER['HTTP_HOST'];

    }

    /**
     * @return string
     */
    #[Pure] public function getHostDomainName(): string
    {

        return gethostbyaddr($this->getHostIp());

    }

    /**
     * @param mixed|null $schema
     *
     * @return string|null
     */
    #[Pure] public function getScheme(mixed $schema = null): ?string
    {

        if (null !== $schema) {
            return sprintf('%s://', $schema);
        }

        if ((int) $_SERVER['SERVER_PORT'] === 443
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
    #[Pure] public function getFullUrl(string $url): string
    {

        return sprintf('%s%s%s', $this->getScheme(), $this->getHost(), $this->trimmingSlashes($url));

    }

    /**
     * @param string $url
     *
     * @return false|int
     */
    private function getStartPositionParameters(string $url): bool|int
    {

        return mb_strripos($this->trimmingSlashes($url), '?');

    }

}