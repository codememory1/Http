<?php

namespace Codememory\HttpFoundation\Client\Header;

use Codememory\HttpFoundation\Exceptions\HeaderNotFoundInSubException;
use Codememory\HttpFoundation\Client\Url;
use JetBrains\PhpStorm\Pure;

/**
 * Class Header
 * @package System\Http\Response\Header
 *
 * @author  Codememory
 */
class Header
{

    /* 1XX */
    public const HTTP_CONTINUE = 100;
    public const HTTP_SWITCHING_PROTOCOLS = 101;
    public const HTTP_PROCESSING = 102;

    /* 2XX */
    public const HTTP_OK = 200;
    public const HTTP_CREATED = 201;
    public const HTTP_ACCEPTED = 202;
    public const HTTP_NON_AUTHORITATIVE_INFO = 203;
    public const HTTP_NO_CONTENT = 204;
    public const HTTP_RESET_CONTENT = 205;
    public const HTTP_PARTIAL_CONTENT = 206;
    public const HTTP_MULTI_STATUS = 207;
    public const HTTP_ALREADY_REPORTED = 208;
    public const HTTP_IM_USED = 226;

    /* 3XXX */
    public const HTTP_MULTIPLE_CHOICES = 300;
    public const HTTP_MOVED_PERMANENTLY = 301;
    public const HTTP_FOUND = 302;
    public const HTTP_SEE_OTHER = 303;
    public const HTTP_NOT_MODIFIED = 304;
    public const HTTP_USE_PROXY = 305;
    public const HTTP_RESERVED = 306;
    public const HTTP_TEMPORARY_REDIRECT = 307;
    public const HTTP_PERMANENT_REDIRECT = 308;

    /* 4XX */
    public const HTTP_BAD_REQUEST = 400;
    public const HTTP_UNAUTHORIZED = 401;
    public const HTTP_PAYMENT_REQUIRED = 402;
    public const HTTP_FORBIDDEN = 403;
    public const HTTP_NOT_FOUND = 404;
    public const HTTP_METHOD_NOT_ALLOWED = 405;
    public const HTTP_NOT_ACCEPTABLE = 406;
    public const HTTP_PROXY_AUTH_REQUIRED = 407;
    public const HTTP_REQUEST_TIMEOUT = 408;
    public const HTTP_CONFLICT = 409;
    public const HTTP_GONE = 410;
    public const HTTP_LENGTH_REQUIRED = 411;
    public const HTTP_PRECONDITION_FAILED = 412;
    public const HTTP_PAYLOAD_TOO_LARGE = 413;
    public const HTTP_URI_TOO_LONG = 414;
    public const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;
    public const HTTP_RANGE_NOT_SATISFIABLE = 416;
    public const HTTP_EXPECTATION_FAILED = 417;
    public const HTTP_TEAPOT = 418;
    public const HTTP_AUTHENTICATION_TIMEOUT = 419;
    public const HTTP_MISDIRECTED_REQUEST = 421;
    public const HTTP_UNPROCESSABLE_ENTITY = 422;
    public const HTTP_LOCKED = 423;
    public const HTTP_FAILED_DEPENDENCY = 424;
    public const HTTP_UPGRADE_REQUIRED = 426;
    public const HTTP_PRECONDITION_REQUIRED = 428;
    public const HTTP_TOO_MANY_REQUESTS = 429;
    public const HTTP_LARGE_HEADER_FILES = 431;
    public const HTTP_RETRY_WITH = 449;
    public const HTTP_UNAVAILABLE_LEGAL_REASONS = 449;
    public const HTTP_CLIENT_CLOSED_REQUEST = 499;

    /* 5XX */
    public const HTTP_INTERNAL_SERVER_ERROR = 500;
    public const HTTP_NOT_IMPLEMENTED = 501;
    public const HTTP_BAD_GATEWAY = 502;
    public const HTTP_SERVICE_UNAVAILABLE = 503;
    public const HTTP_GATEWAY_TIMEOUT = 504;
    public const HTTP_HTTP_VERSION_NOT_SUPPORTED = 505;
    public const HTTP_VARIANT_ALSO_NEGOTIATES = 506;
    public const HTTP_INSUFFICIENT_STORAGE = 507;
    public const HTTP_LOOP_DETECTED = 508;
    public const HTTP_BANDWIDTH_LIMIT_EXCEEDED = 509;
    public const HTTP_NOT_EXTENDED = 510;
    public const HTTP_NETWORK_AUTH_REQUIRED = 511;
    public const HTTP_UNKNOWN_ERROR = 520;
    public const HTTP_WEB_SERVER_IS_DOWN = 521;
    public const HTTP_CONNECTION_TIMED_OUT = 522;
    public const HTTP_ORIGIN_IS_UNREACHABLE = 523;
    public const HTTP_TIMEOUT_OCCURRED = 524;
    public const HTTP_SSL_HANDSHAKE_FAILED = 525;
    public const HTTP_INVALID_SSL_CERTIFICATE = 526;

    /**
     * @var array|string[]
     */
    private array $httpCodeText = [

        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',

        200 => 'Ok',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported ',
        226 => 'Im Used',

        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Reserved',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',

        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I’m a teapot',
        419 => 'Authentication Timeout',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Request',
        431 => 'Request Header Fields Too Large',
        449 => 'Retry With',
        451 => 'Unavailable For Legal Reasons',
        499 => 'Client Closed Request',

        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
        520 => 'Unknown Error',
        521 => 'Web Server Is Down',
        522 => 'Connection Timed Out',
        523 => 'Origin Is Unreachable',
        524 => 'A Timeout Occurred',
        525 => 'SSL Handshake Failed',
        526 => 'Invalid SSL Certificate'

    ];

    /**
     * @var int|float
     */
    private int|float $versionProtocol = 1.1;

    /**
     * @var array
     */
    private array $sentHeaders = [];

    /**
     * @var int
     */
    private int $responseCode = self::HTTP_OK;

    /**
     * @var bool
     */
    private bool $replaceHeaders = true;

    /**
     * @var Parser
     */
    public Parser $parser;

    /**
     * Header constructor.
     */
    #[Pure] public function __construct()
    {

        $this->parser = new Parser();

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>
     * Header Parser
     * <=<=<=<=<=<=<=<=<=<=
     *
     * @return Parser
     *
     */
    public function getParser(): Parser
    {

        return $this->parser;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Set the HTTP protocol version. Default 1.1
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param int|float $version
     *
     * @return $this
     */
    public function setProtocolVersion(int|float $version): Header
    {

        $this->versionProtocol = $version;

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Whether to replace old headers with sent ones. Otherwise
     * several identical titles will be created.
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param bool $replace
     *
     * @return $this
     */
    public function replaceHeaders(bool $replace): Header
    {

        $this->replaceHeaders = $replace;

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Set headers by passing an array as an argument, in which the
     * key is the name of the header, and its value is the value of the header
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array $headers
     * @param bool  $replace
     *
     * @return $this
     */
    public function set(array $headers, bool $replace = true): Header
    {

        foreach ($headers as $header => $value) {
            $this->sentHeaders[$header] = $value;
        }

        $this->replaceHeaders($replace);

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Set/Change Content Type
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $type
     *
     * @return $this
     */
    public function setContentType(string $type): Header
    {

        $this->sentHeaders['Content-Type'] = $type;

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Set/Change content encoding
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $charset
     *
     * @return Header
     * @throws HeaderNotFoundInSubException
     */
    public function setCharset(string $charset): Header
    {

        return $this->addToSentHeader(
            'Content-Type', sprintf('; charset=%s', $charset)
        );

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Set default response code response status 200 OK
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param int $code
     *
     * @return $this
     */
    public function setResponseCode(int $code): Header
    {

        $this->responseCode = $code;

        return $this;

    }


    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * A method that works with the "addToSentHeader" method that adds a value
     * separating it with signs
     * ----------------------------------------------------------------------------------
     * addEnum(['utf-8', 'windows-1251'], ['qr'])
     *
     * And the method will generate a string like this
     * utf-8, windows-1251; qr
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array ...$data
     *
     * @return string|null
     */
    #[Pure] public function addEnum(array ...$data): ?string
    {

        $complete = null;

        foreach ($data as $enum) {
            foreach ($enum as $item) {
                $complete .= $item . ',';
            }

            $complete = substr($complete, 0, -1) . ';';
        }

        return $complete;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Get a ticking HTTP response code
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return int
     */
    public function getHttpStatus(): int
    {

        return http_response_code();

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Get an array or a specific title from all available
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string ...$headers
     *
     * @return string|array|null
     */
    public function getHeader(string ...$headers): string|array|null
    {

        $list = [];

        if (count($headers) > 1) {
            foreach ($headers as $header) {
                if ($this->hasHeader($header)) {
                    $list[$header] = $this->getAll()[$header];
                }
            }
        } else if ($this->hasHeader($headers[0])) {
            $list = $this->getAll()[$headers[0]];
        }

        return $list;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Get an array of all available headers from response and request
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return array
     */
    public function getAll(): array
    {

        return array_merge(
            apache_request_headers(),
            apache_response_headers(),
            get_headers(Url::hostWithSchema(Url::getHostIp()), 1)
        );

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Check for header existence
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasHeader(string $key): bool
    {

        return array_key_exists($key, $this->getAll());

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Remove a title or multiple titles from those present
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string ...$headers
     *
     * @return bool
     */
    public function removeHeaders(string ...$headers): bool
    {

        foreach ($headers as $header) {
            if ($this->hasHeader($header)) {
                header_remove($header);
            }
        }

        return true;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Send all prepared headers and response code
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return $this
     */
    public function send(): Header
    {

        $this
            ->sendingHeaders()
            ->sendResponseCode();

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Sending template for all prepared headers
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return Header
     */
    private function sendingHeaders(): Header
    {

        if ([] !== $this->sentHeaders) {
            foreach ($this->sentHeaders as $header => $value) {
                header(
                    sprintf('%s: %s', $header, $value), $this->replaceHeaders
                );
            }
        }

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Stub send header HTTP response code and set response code
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return void
     */
    private function sendResponseCode(): void
    {

        $http = sprintf(
            'HTTP/%s %s %s',
            $this->versionProtocol,
            $this->responseCode,
            $this->httpCodeText[$this->responseCode]
        );

        header($http, true);
        http_response_code($this->responseCode);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Add a new value to the expected header, if there is no such header
     * an exception will be thrown
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $header
     * @param string $value
     *
     * @return Header
     * @throws HeaderNotFoundInSubException
     */
    private function addToSentHeader(string $header, string $value): Header
    {

        if (false === array_key_exists($header, $this->sentHeaders)) {
            throw new HeaderNotFoundInSubException($header);
        }

        $this->sentHeaders[$header] .= $value;

        return $this;

    }

}