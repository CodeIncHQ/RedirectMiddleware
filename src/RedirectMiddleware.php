<?php
//
// +---------------------------------------------------------------------+
// | CODE INC. SOURCE CODE                                               |
// +---------------------------------------------------------------------+
// | Copyright (c) 2018 - Code Inc. SAS - All Rights Reserved.           |
// | Visit https://www.codeinc.fr for more information about licensing.  |
// +---------------------------------------------------------------------+
// | NOTICE:  All information contained herein is, and remains the       |
// | property of Code Inc. SAS. The intellectual and technical concepts  |
// | contained herein are proprietary to Code Inc. SAS are protected by  |
// | trade secret or copyright law. Dissemination of this information or |
// | reproduction of this material  is strictly forbidden unless prior   |
// | written permission is obtained from Code Inc. SAS.                  |
// +---------------------------------------------------------------------+
//
// Author:   Joan Fabrégat <joan@codeinc.fr>
// Date:     28/05/2018
// Time:     13:37
// Project:  RedirectMiddleware
//
declare(strict_types=1);
namespace CodeInc\RedirectMiddleware;
use CodeInc\RedirectMiddleware\Tests\RedirectMiddlewareTest;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;


/**
 * Class RedirectMiddleware
 *
 * @package CodeInc\RedirectMiddleware
 * @author  Joan Fabrégat <joan@codeinc.fr>
 * @license MIT <https://github.com/CodeIncHQ/RedirectMiddleware/blob/master/LICENSE>
 * @link https://github.com/CodeIncHQ/RedirectMiddleware
 * @see RedirectMiddlewareTest
 */
class RedirectMiddleware implements MiddlewareInterface
{
    public const DEFAULT_URI_PATH = '/go';
    public const DEFAULT_QUERY_PARAMETER = 'to';

    /**
     * @var string
     */
    private $uriPath;

    /**
     * @var string
     */
    private $queryParameter;

    /**
     * RedirectMiddleware constructor.
     *
     * @param string $uriPath
     * @param string $queryPrameter
     */
    public function __construct(string $uriPath = self::DEFAULT_URI_PATH,
        string $queryPrameter = self::DEFAULT_QUERY_PARAMETER)
    {
        $this->uriPath = $uriPath;
        $this->queryParameter = $queryPrameter;
    }

    /**
     * @return string
     */
    public function getUriPath():string
    {
        return $this->uriPath;
    }

    /**
     * @return string
     */
    public function getQueryParameter():string
    {
        return $this->queryParameter;
    }

    /**
     * @inheritdoc
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler):ResponseInterface
    {
        // sends a redirect response
        if ($this->isRedirectRequest($request))
        {
            return new RedirectResponse($this->getRequestRedirectUrl($request));
        }

        // uses the provided handler
        return $handler->handle($request);
    }

    /**
     * Verifies if the requrest is a redirect request.
     *
     * @param ServerRequestInterface $request
     * @return bool
     */
    protected function isRedirectRequest(ServerRequestInterface $request):bool
    {
        return $request->getUri()->getPath() == $this->getUriPath()
            && isset($request->getQueryParams()[$this->getQueryParameter()]);
    }

    /**
     * @param ServerRequestInterface $request
     * @return null|string
     */
    protected function getRequestRedirectUrl(ServerRequestInterface $request):?string
    {
        return $request->getQueryParams()[$this->getQueryParameter()] ?? null;
    }

    /**
     * Builds a URI to the redirect middleware.
     *
     * @param string $destinationUrl
     * @return string
     */
    public function builRedirectUri(string $destinationUrl):string
    {
        return "{$this->getUriPath()}?{$this->getQueryParameter()}=".urlencode($destinationUrl);
    }
}