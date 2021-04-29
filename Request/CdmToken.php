<?php

namespace Codememory\Http\Request;

use Exception;
use Codememory\Http\Client\Header\Header;
use Codememory\Components\JsonParser\JsonParser;
use Codememory\Http\Request\Exceptions\CdmTokenNameErrorException;
use Codememory\Http\Request\Exceptions\TokenGenerationErrorException;
use Codememory\Components\JsonParser\Exceptions\JsonErrorException;

/**
 * Class CdmToken
 * @package System\Http\Request
 *
 * @author  Codememory
 */
class CdmToken
{

    private const HEADER_NAME = 'CDM-TOKEN';
    private const SESSION_NAME = 'cdm:token';
    public const INPUT_NAME = 'cdm-token_%s';

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
     * & Set the name of the token to be verified
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): CdmToken
    {

        $this->name = $name;

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Generate a 32 byte token with which verification will occur
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return $this
     * @throws CdmTokenNameErrorException
     * @throws Exception
     */
    public function generate(): CdmToken
    {

        if (null === $this->name) {
            throw new CdmTokenNameErrorException(
                'The token cannot be generated because the token name is not specified'
            );
        }

        $this->token = bin2hex(random_bytes(32));

        $this->tokenInfo[$this->name] = $this->token;

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Set token validation status via headers. That is, if the token does
     * & not match the token which in the header the verification will not be passed
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param bool $check
     *
     * @return $this
     */
    public function checkInHeaders(bool $check): CdmToken
    {

        $this->checkInHeaders = $check;

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Get the generated token, if the token is not generated, a
     * & TokenGenerationErrorException will be thrown
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
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & The method returns an array of all tokens from the session
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
     * & The main method for sending tokens to headers in json format
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array $tokens
     *
     * @return CdmToken
     * @throws JsonErrorException
     */
    private function sendHeader(array $tokens): CdmToken
    {

        $this->header->set([
            self::HEADER_NAME => $this->jsonParser->setData($tokens)->encode()
        ])->send();

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Handler for sending tokens to headers
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return CdmToken
     * @throws JsonErrorException
     */
    private function sendToHeaders(): CdmToken
    {

        if (true === $this->checkInHeaders) {
            $tokens = $this->tokensOfSession();

            foreach ($tokens as $name => $token) {
                $tokens[$name] = $token;
            }

            return $this->sendHeader($tokens);
        }

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & The main method for sending all tokens to the session
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param array $tokens
     *
     * @return CdmToken
     */
    private function sendSession(array $tokens): CdmToken
    {

        $this->request->session->set(self::SESSION_NAME, $tokens);

        return $this;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Handler for sending tokens to session
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return CdmToken
     */
    private function sendToSession(): CdmToken
    {

        $tokens = $this->tokensOfSession();

        if ([] !== $tokens) {
            $tokens[$this->name] = $this->token;

            return $this->sendSession($tokens);
        }

        return $this->sendSession($this->tokenInfo);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Get a token from a request, namely it can be a token from a form
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $name
     *
     * @return string|null
     */
    private function getRequestToken(string $name): ?string
    {

        return $this->request->post(
            sprintf(self::INPUT_NAME, $name)
        );

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Handler method that matches the token from the request with
     * & the token in the session
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $name
     *
     * @return bool
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
     * & A handler method that checks the token from the request against the
     * & token in the headers, for this, the token verification status must be
     * & true by default checkInHeaders = true
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
     * & Checking for existence in a session by its name. Why in session?
     * & Because CdmToken works with session by default, and checking with
     * & headers is an additional part.
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
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & The method removes all generated tokens that exist in the request.
     * & The method is triggered when tokens are verified
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     */
    private function removeToken(): void
    {

        $this->request->session->remove(self::SESSION_NAME);
        $this->header->removeHeaders(self::HEADER_NAME);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & The main method of token verification. The method accepts an argument
     * & of the names of tokens to be checked, if a non-existing token name
     * & is specified, an exception CdmTokenNameErrorException will be thrown
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string ...$names
     *
     * @return bool
     * @throws CdmTokenNameErrorException
     * @throws JsonErrorException
     */
    public function tokenVerification(string ...$names): bool
    {

        $this->sendToHeaders();
        $status = false;

        foreach ($names as $name) {
            if (false === $this->existName($name)) {
                throw new CdmTokenNameErrorException(
                    sprintf('To verify the token, all names must be specified correctly. Token named <b>%s</b> is not created', $name)
                );
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
     * & This method sends tokens to the session. The method is called
     * & when you need to generate a token and get it
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return CdmToken
     */
    public function send(): CdmToken
    {

        $this->sendToSession();

        return clone $this;

    }

}