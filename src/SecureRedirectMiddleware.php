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
use CodeInc\RedirectMiddleware\Tests\SecureRedirectMiddlewareTest;
use Firebase\JWT\JWT;
use Psr\Http\Message\ServerRequestInterface;


/**
 * Class SecureRedirectMiddleware
 *
 * @package CodeInc\RedirectMiddleware
 * @author  Joan Fabrégat <joan@codeinc.fr>
 * @license MIT <https://github.com/CodeIncHQ/RedirectMiddleware/blob/master/LICENSE>
 * @link https://github.com/CodeIncHQ/RedirectMiddleware
 * @see SecureRedirectMiddlewareTest
 */
class SecureRedirectMiddleware extends RedirectMiddleware
{
    public const JWT_ALGO = 'HS256';

    /**
     * @var string|null
     */
    private $jwtKey;

    /**
     * SecureRedirectMiddleware constructor.
     *
     * @param string $jwtKey
     * @param string $uriPath
     * @param string $queryPrameter
     * @throws RedirectMiddlewareException
     */
    public function __construct(string $jwtKey, string $uriPath = self::DEFAULT_URI_PATH,
        string $queryPrameter = self::DEFAULT_QUERY_PARAMETER)
    {
        $this->setJwtKey($jwtKey);
        parent::__construct($uriPath, $queryPrameter);
    }

    /**
     * Sets the JWT key.
     *
     * @param string $jwtKey
     * @throws RedirectMiddlewareException
     */
    protected function setJwtKey(string $jwtKey):void
    {
        if (empty($jwtKey)) {
            throw new RedirectMiddlewareException("The JWT key can not be empty", $this);
        }
        $this->jwtKey = $jwtKey;
    }

    /**
     * @inheritdoc
     * @param ServerRequestInterface $request
     * @return null|string
     * @throws RedirectMiddlewareException
     */
    protected function getRequestRedirectUrl(ServerRequestInterface $request):?string
    {
            if (isset($request->getQueryParams()[$this->getQueryParameter()])) {
                return $this->decodeUrlJwt($request->getQueryParams()[$this->getQueryParameter()]);
            }
            return null;

    }

    /**
     * Decodes and returns a JWT containing a redirect URL. If the JWT does not contain a redirect URL the method
     * returns NULL.
     *
     * @param string $jwt
     * @return null|string
     * @throws RedirectMiddlewareException
     */
    public function decodeUrlJwt(string $jwt):?string
    {
        try {
            return JWT::decode($jwt, $this->jwtKey, [self::JWT_ALGO])->RedirectUrl ?? null;
        }
        catch (\Throwable $exception) {
            throw new RedirectMiddlewareException(
                sprintf("Error while decoding the URL JWT '%s'",
                    $jwt),
                $this,
                0,
                $exception
            );
        }
    }

    /**
     * Encodes and returns a JWT containing a redirect URL.
     *
     * @param string $url
     * @return string
     * @throws RedirectMiddlewareException
     */
    public function encodeUrlJwt(string $url):string
    {
        try {
            return JWT::encode(['RedirectUrl' => $url], $this->jwtKey, self::JWT_ALGO);
        }
        catch (\Throwable $exception) {
            throw new RedirectMiddlewareException(
                sprintf("Error while building the JWT for the URL '%s'", $url),
                $this,
                0,
                $exception
            );
        }
    }

    /**
     * @inheritdoc
     * @param string $destinationUrl
     * @return string
     * @throws RedirectMiddlewareException
     */
    public function builRedirectUri(string $destinationUrl):string
    {
        return parent::builRedirectUri(
            $this->encodeUrlJwt($destinationUrl)
        );
    }
}