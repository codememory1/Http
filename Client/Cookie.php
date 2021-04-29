<?php

namespace Codememory\Http\Client;

use Codememory\Components\DateTime\DateTime;
use Codememory\Components\DateTime\Exceptions\InvalidTimezoneException;
use Codememory\Http\Client\Header\Header;
use JetBrains\PhpStorm\Pure;
use System\Http\Exceptions\Cookie\InvalidCookieNameException;
use Codememory\Http\Client\Exceptions\InvalidSameSiteException;
use Codememory\Http\Client\Exceptions\NotSpecifiedSecureException;

/**
 * Class Cookie
 * @package System\Http\Client
 *
 * @author  Codememory
 */
class Cookie
{

    public const SAME_SITE_LAX = 'Lax';
    public const SAME_SITE_STRICT = 'Strict';
    public const SAME_SITE_NONE = 'None';

    /**
     * @var string|null
     */
    private ?string $name = null;

    /**
     * @var string|int|float|null
     */
    private string|int|float|null $value = null;

    /**
     * @var string|null
     */
    private ?string $domain = null;

    /**
     * @var string|null
     */
    private ?string $path = null;

    /**
     * @var int
     */
    private int $expires = 0;

    /**
     * @var bool
     */
    private bool $httpOnly = false;

    /**
     * @var bool
     */
    private bool $secure = false;

    /**
     * @var array|string[][]
     */
    private array $chars = [
        'forbiddenChar'        => [
            '(', ')', '<', '>', '@', ':', '/', '[', ']', '?', '=', '{', '}', ' '
        ],
        'forbiddenCharValues'  => [
            '"', ',', ';', '\\'
        ],
        'forbiddenCharToASCLI' => [
            '%22', '%2C', '%3B', '%5C'
        ]
    ];

    /**
     * @var string|null
     */
    private ?string $sameSite = self::SAME_SITE_LAX;

    /**
     * @var Header
     */
    private Header $header;

    /**
     * Cookie constructor.
     *
     * @param Header $header
     */
    #[Pure] public function __construct(Header $header)
    {
        
        $this->header = $header;
        
    }
    
    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Short syntax for creating a cookie header
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string                $name   Cookie name
     * @param string|int|float|null $value  Cookie value
     * @param string|null           $domain Domain - example.com
     * @param string|null           $path   Path - /
     * @param int                   $life
     * @param bool                  $httpOnly
     * @param bool                  $secure
     * @param string|null           $sameSite
     *
     * @return $this
     * @throws InvalidCookieNameException
     * @throws InvalidSameSiteException
     * @throws NotSpecifiedSecureException
     * @throws InvalidTimezoneException
     */
    public function create(
        string $name,
        string|int|float|null $value = null,
        ?string $domain = null,
        ?string $path = null,
        int $life = 0,
        bool $httpOnly = false,
        bool $secure = false,
        ?string $sameSite = null): Cookie
    {

        $this
            ->setName($name)
            ->setValue($value)
            ->setDomain($domain)
            ->setPath($path)
            ->setExpires($life)
            ->setHttpOnly($httpOnly)
            ->setSecure($secure)
            ->setSameSite($sameSite)
            ->checkEvery()
            ->send();

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Get an array of all sourced cookies
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return array
     */
    public function all(): array
    {

        $cookie = $this->header->getHeader('Cookie');
        $cookies = $this->header->getParser()
            ->factor([] === $cookie ? null : $cookie);
        $readyCookies = [];

        if ([] !== $cookies && !empty($cookies[0])) {
            foreach ($cookies as $cookie) {
                [$cookieName, $value] = explode('=', trim($cookie));

                $readyCookies[$cookieName] = str_replace(
                    $this->chars['forbiddenCharValues'], $this->chars['forbiddenCharToASCLI'], $value
                );
            }
        }

        return $readyCookies;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Returns true if there are no cookies
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $name
     *
     * @return bool
     */
    public function missing(string $name): bool
    {

        return false === array_key_exists($name, $this->all());

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Handle the cookie missing event using a callback
     *
     * Arguments:
     * $cookie    - $this
     * $name      - the name of the transmitted cookie
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string   $name
     * @param callable $callback
     *
     * @return $this
     */
    public function whenMissing(string $name, callable $callback): Cookie
    {

        if ($this->missing($name)) {
            call_user_func($callback, clone $this, $name);
        }

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Get a cookie by its name, null will be returned if missing
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $name
     *
     * @return int|string|null
     */
    public function get(string $name): null|int|string
    {

        if (false === $this->missing($name)) {
            return $this->all()[$name];
        }

        return null;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Delete cookies by name. If there is no cookie, no cookie will be created
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $name
     *
     * @return bool
     * @throws InvalidCookieNameException
     * @throws InvalidSameSiteException
     * @throws InvalidTimezoneException
     * @throws NotSpecifiedSecureException
     */
    public function remove(string $name): bool
    {

        $this->create($name, life: -1);

        return true;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Sending a cookie header
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return $this
     * @throws InvalidTimezoneException
     */
    public function send(): Cookie
    {

        $this->header->set([
            'Set-Cookie' => $this->renderHeader()
        ])->send();

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Reader directive for cookies.
     *
     * & Example: HttpOnly; Secure
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return string|null
     */
    private function renderDirectives(): ?string
    {

        $directives = null;
        $array = [
            '; HttpOnly'                  => $this->httpOnly,
            '; Secure'                    => $this->secure,
            '; Domain=%s' . $this->domain => $this->domain,
            '; Path=' . $this->path       => $this->path
        ];

        foreach ($array as $str => $directive) {
            if (null !== $directive && false !== $directive) {
                $directives .= $str;
            }
        }

        return $directives;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & The main method of rendering the title and all directives
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return string
     * @throws InvalidTimezoneException
     */
    private function renderHeader(): string
    {

        $datetime = new DateTime();

        $dispatched = sprintf(
            '%s=%s; SameSite=%s%s; expires=%s',
            $this->name, $this->value, $this->sameSite, $this->renderDirectives(), $datetime->format('D, d-M-Y')
        );

        if (0 !== $this->getExpires()) {
            $dispatched .= sprintf('; Max-age=%s', $this->getExpires());
        }

        return $dispatched;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & The main method for validating directives. Which invokes other methods for
     * & validating the directive
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return Cookie
     * @throws InvalidCookieNameException
     * @throws InvalidSameSiteException
     * @throws NotSpecifiedSecureException
     */
    private function checkEvery(): Cookie
    {

        return $this
            ->nameValidation()
            ->sameSiteValidation();

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Validate cookie name. Check for occupancy and check for reserved characters
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return Cookie
     * @throws InvalidCookieNameException
     */
    private function nameValidation(): Cookie
    {

        $chars = array_merge($this->chars['forbiddenChar'], $this->chars['forbiddenCharValues']);
        if (null === $this->name || strpbrk($this->name, implode('', $chars))) {
            throw new InvalidCookieNameException(implode(', ', $this->chars['forbiddenChar']));
        }

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & SameSite validation check that the selected SameSite was reserved,
     * & and if SameSite: None was selected, then Secure should also be specified
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return Cookie
     * @throws InvalidSameSiteException
     * @throws NotSpecifiedSecureException
     */
    private function sameSiteValidation(): Cookie
    {

        $sameSite = ucfirst($this->sameSite);
        $list = [
            self::SAME_SITE_LAX,
            self::SAME_SITE_NONE,
            self::SAME_SITE_STRICT,
            null
        ];

        if (false === in_array($sameSite, $list)) {
            throw new InvalidSameSiteException($sameSite, implode(', ', $list));
        } elseif (strcasecmp($sameSite, self::SAME_SITE_NONE) === 0 && false === $this->secure) {
            throw new NotSpecifiedSecureException(self::SAME_SITE_NONE);
        }

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Get the name of the cookie being created
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return string|null
     */
    public function getName(): ?string
    {

        return $this->name;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Set cookie name reserved characters will be converted to ASCII
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string|null $name
     *
     * @return Cookie
     */
    public function setName(?string $name): Cookie
    {
        $this->name = str_replace($this->chars['forbiddenCharValues'], $this->chars['forbiddenCharToASCLI'], $name);

        return $this;
    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Get the value of the generated cookie
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return float|int|string|null
     */
    public function getValue(): float|int|string|null
    {

        return $this->value;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Set a value for cookies, reserved characters will be converted to ASCII
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param float|int|string|null $value
     *
     * @return $this
     */
    public function setValue(float|int|string|null $value): Cookie
    {

        $this->value = str_replace($this->chars['forbiddenCharValues'], $this->chars['forbiddenCharToASCLI'], $value);

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Get the domain setting for the created cookie
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return string|null
     */
    public function getDomain(): ?string
    {

        return $this->domain;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Set domain for generated cookie
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string|null $domain
     *
     * @return $this
     */
    public function setDomain(?string $domain): Cookie
    {

        $this->domain = $domain;

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Get the path of the generated cookie
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return string|null
     */
    public function getPath(): ?string
    {

        return $this->path;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Set path for generated cookie
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string|null $path
     *
     * @return $this
     */
    public function setPath(?string $path): Cookie
    {

        $this->path = $path;

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Get the expires of the generated cookie
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return int
     */
    public function getExpires(): int
    {

        return $this->expires;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Set expires for generated cookie
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param int $expires
     *
     * @return $this
     */
    public function setExpires(int $expires): Cookie
    {

        $this->expires = $expires;

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Is the cookie being created HttpOnly
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return bool
     */
    public function isHttpOnly(): bool
    {

        return $this->httpOnly;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Set HttpOnly for generated cookie
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param bool $httpOnly
     *
     * @return $this
     */
    public function setHttpOnly(bool $httpOnly): Cookie
    {

        $this->httpOnly = $httpOnly;

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Is the cookie being created Secure
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return bool
     */
    public function isSecure(): bool
    {

        return $this->secure;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Set Secure for generated cookie
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param bool $secure
     *
     * @return $this
     */
    public function setSecure(bool $secure): Cookie
    {

        $this->secure = $secure;

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Get the SameSite generated by the cookie
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return string|null
     */
    public function getSameSite(): ?string
    {

        return $this->sameSite;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Set SameSite for generated cookie
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string|null $sameSite
     *
     * @return $this
     */
    public function setSameSite(?string $sameSite): Cookie
    {

        $this->sameSite = $sameSite;

        return $this;

    }

}