<?php

namespace Codememory\HttpFoundation\Response;

use Codememory\HttpFoundation\Client\Header\Header;
use Codememory\HttpFoundation\Interfaces\RedirectInterface;
use Codememory\HttpFoundation\Request\Request;

/**
 * Class RedirectResponse
 * @package Codememory\HttpFoundation
 *
 * @author  Codememory
 */
class RedirectResponse extends Response implements RedirectInterface
{

    private const SESSION_NAME_PREVIOUS_URL = '_cdm-previous-url';

    /**
     * @var Request
     */
    private Request $request;

    /**
     * RedirectResponse constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {

        $this->request = $request;

        parent::__construct(new Header());

    }

    /**
     * @inheritDoc
     */
    public function redirect(string $url, int $responseCode = 302, array $headers = []): RedirectInterface
    {

        $headers['Location'] = $url;

        $this->setHeaders($headers)->setResponseCode($responseCode);

        if (301 === $responseCode) {
            $this->header->removeHeaders('cache-control');
        }

        $this->sendHeaders();

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function refresh(int $responseCode = 302, array $headers = []): RedirectInterface
    {

        return $this->redirect($this->request->url->current(), $responseCode, $headers);

    }

    /**
     * @inheritDoc
     */
    public function setPreviousUrl(string $url): RedirectInterface
    {

        $this->request->session->set(self::SESSION_NAME_PREVIOUS_URL, $url);

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function previous(int $responseCode = 302, array $headers = []): RedirectInterface
    {

        $previousUrl = $this->request->session->get(self::SESSION_NAME_PREVIOUS_URL);

        if (null === $previousUrl) {
            return $this->refresh($responseCode, $headers);
        }

        return $this->redirect($previousUrl, $responseCode, $headers);

    }

}