<?php

namespace Codememory\HttpFoundation\Request;

use Codememory\Components\JsonParser\Exceptions\JsonErrorException;
use Codememory\Components\JsonParser\JsonParser;
use Codememory\HttpFoundation\Client\Header\Header;
use Codememory\HttpFoundation\Exceptions\TokenGenerationErrorException;
use Codememory\HttpFoundation\Exceptions\TokenNameErrorException;
use Exception;

/**
 * Class Token
 * @package Codememory\HttpFoundation\Request
 *
 * @author  Codememory
 */
class Token
{

    public const INPUT_NAME = 'cdm-token_%s';
    private const HEADER_NAME = 'CDM-TOKEN';
    private const SESSION_NAME = 'cdm:token';

    /**
     * @var Request
     */
    private Request $request;

    /**
     * @var string|null
     */
    private ?string $name = null;

    /**
     * @var string|null
     */
    private ?string $token = null;

    /**
     * @var array
     */
    private array $tokenInfo = [];

    /**
     * @var bool
     */
    private bool $checkInHeaders = true;

    /**
     * @var Header
     */
    private Header $header;

    /**
     * @var JsonParser
     */
    private JsonParser $jsonParser;

    /**
     * CdmToken constructor.
     *
     * @param Request    $request
     * @param Header     $header
     * @param JsonParser $jsonParser
     */
    public function __construct(Request $request, Header $header, JsonParser $jsonParser)
    {

        $this->request = $request;
        $this->header = $header;
        $this->jsonParser = $jsonParser;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Set the name of the token to be verified
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $name
     *
     * @return Token
     */
    public function setName(string $name): Token
    {

        $this->name = $name;

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Generate a 32 byte token with which verification will occur
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return Token
     * @throws TokenNameErrorException
     * @throws Exception
     */
    public function generate(): Token
    {

        if (null === $this->name) {
            throw new TokenNameErrorException('The token cannot be generated because the token name is not specified');
        }

        $this->token = bin2hex(random_bytes(32));

        $this->tokenInfo[$this->name] = $this->token;

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Set token validation status via headers. That is, if the token does
     * not match the token which in the header the verification will not be passed
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param bool $check
     *
     * @return Token
     */
    public function checkInHeaders(bool $check): Token
    {

        $this->checkInHeaders = $check;

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Get the generated token, if the token is not generated, a
     * TokenGenerationErrorException will be thrown
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return string
     * @throws TokenGenerationErrorException
     */
    public function get(): string
    {

        if (null === $this->token) {
            throw new TokenGenerationErrorException();
        }

        return $this->token;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * The main method of token verification. The method accepts an argument
     * of the names of tokens to be checked, if a non-existing token name
     * is specified, an exception TokenNameErrorException will be thrown
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string ...$names
     *
     * @return bool
     * @throws JsonErrorException
     * @throws TokenNameErrorException
     */
    public function tokenVerification(string ...$names): bool
    {

        $this->sendToHeaders();
        $status = false;

        foreach ($names as $name) {
            if (false === $this->existName($name)) {
                throw new TokenNameErrorException(sprintf(
                    'To verify the token, all names must be specified correctly. Token named <b>%s</b> is not created',
                    $name
                ));
            } else {
                $status = $this->tokenValidationWithSession($name)
                    && $this->tokenValidationWithHeaders($name);

                if (true !== $status) {
                    $status = false;

                    break;
                }
            }
        }

        $this->removeToken();

        return $status;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * This method sends tokens to the session. The method is called
     * when you need to generate a token and get it
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return Token
     */
    public function send(): Token
    {

        $this->sendToSession();

        return clone $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Checking for existence in a session by its name. Why in session?
     * Because CdmToken works with session by default, and checking with
     * headers is an additional part.
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $name
     *
     * @return bool
     */
    public function existName(string $name): bool
    {

        return array_key_exists($name, $this->tokensOfSession());

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * The method returns an array of all tokens from the session
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return array
     */
    private function tokensOfSession(): array
    {

        return $this->request->session->get(self::SESSION_NAME) ?? [];

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * The main method for sending tokens to headers in json format
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array $tokens
     *
     * @return void
     * @throws JsonErrorException
     */
    private function sendHeader(array $tokens): void
    {

        $this->header->set([
            self::HEADER_NAME => $this->jsonParser->setData($tokens)->encode()
        ])->send();

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Handler for sending tokens to headers
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return void
     * @throws JsonErrorException
     */
    private function sendToHeaders(): void
    {

        if (true === $this->checkInHeaders) {
            $tokens = $this->tokensOfSession();

            foreach ($tokens as $name => $token) {
                $tokens[$name] = $token;
            }

            $this->sendHeader($tokens);
        }

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * The main method for sending all tokens to the session
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array $tokens
     *
     */
    private function sendSession(array $tokens): void
    {

        $this->request->session->set(self::SESSION_NAME, $tokens);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Handler for sending tokens to session
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return void
     */
    private function sendToSession(): void
    {

        $tokens = $this->tokensOfSession();

        if ([] !== $tokens) {
            $tokens[$this->name] = $this->token;

            $this->sendSession($tokens);
            return;
        }

        $this->sendSession($this->tokenInfo);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Get a token from a request, namely it can be a token from a form
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $name
     *
     * @return string|null
     * @throws JsonErrorException
     */
    private function getRequestToken(string $name): ?string
    {

        return $this->request->post()->get(sprintf(self::INPUT_NAME, $name));

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Handler method that matches the token from the request with
     * the token in the session
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $name
     *
     * @return bool
     * @throws JsonErrorException
     */
    private function tokenValidationWithSession(string $name): bool
    {

        $token = $this->tokensOfSession()[$name] ?? null;

        if ($token !== $this->getRequestToken($name)) {
            return false;
        }

        return true;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * A handler method that checks the token from the request against the
     * token in the headers, for this, the token verification status must be
     * true by default checkInHeaders = true
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $name
     *
     * @return bool
     * @throws JsonErrorException
     */
    private function tokenValidationWithHeaders(string $name): bool
    {

        if (true === $this->checkInHeaders) {
            $token = $this->header->getHeader(self::HEADER_NAME);

            if ([] !== $token) {
                $token = $this->jsonParser->setData($token)->decode()[$name] ?? null;
            }

            if ($token !== $this->getRequestToken($name)) {
                return false;
            }

            return true;
        }

        return true;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * The method removes all generated tokens that exist in the request.
     * The method is triggered when tokens are verified
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return void
     */
    private function removeToken(): void
    {

        $this->request->session->remove(self::SESSION_NAME);
        $this->header->removeHeaders(self::HEADER_NAME);

    }

}