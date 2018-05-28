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
// Time:     14:14
// Project:  RedirectMiddleware
//
declare(strict_types=1);
namespace CodeInc\RedirectMiddleware\Tests;
use CodeInc\MiddlewareTestKit\FakeRequestHandler;
use CodeInc\MiddlewareTestKit\FakeServerRequest;
use CodeInc\RedirectMiddleware\RedirectResponse;
use CodeInc\RedirectMiddleware\SecureRedirectMiddleware;
use PHPUnit\Framework\TestCase;


/**
 * Class SecureRedirectMiddlewareTest
 *
 * @uses SecureRedirectMiddleware
 * @package CodeInc\RedirectMiddleware\Tests
 * @author  Joan Fabrégat <joan@codeinc.fr>
 */
class SecureRedirectMiddlewareTest extends TestCase
{
    private const TEST_URL = 'https://www.example.com';
    private const TEST_DATA ='b4e58fb0d61c67415f3210f473cf3688c7bb1a34c03b1106db79ab09ef9adafca023720554659206ea'
        .'277c5a8746c2793e111fb4c96ee71dbb928453631b45256d52baa411fa5dd2b4aead2e614018fd58e5cfcc6b52ec741182633a'
        .'47d62d5a7499b10fb5476afda5aa46be877d7c86b9f2f854c2c2b24a2b44a0aed867e6a70cd657c8a87eb71ecf6cc14a04f18e'
        .'9481f7761234ec1b8e2f30181a4c9650ca3189b75659c277634a725839daf1ee9414e6405d49940e3b9522ba735924dd17e27e'
        .'7e5cd17322d3a03300b723d15b28c9213a9d8f4e88f97ba48e88bb03d0c1ccaaf558712c4da167a0d40d82f6d996fa8f8ecbb5'
        .'4b7bf16422d6546f994aa3a425419927855397e44f02e8dcd1b606842e41f6d5cc8e5898b57dd343a03c5cde552ca1ae3724e9'
        .'b052c4ef3d3f49575ee1a8d70ed0e1f783e6a4869b49b3c7400b09a1d7d0db7a3e21f818746fb92a68ec1757415aab8048f968'
        .'a553616894ac78c67ad2150267d532abbb7c3f9254d6edb6676aa92daa90c259de8c297a9ccac40ce4d831a1ff392b36bbfe7e'
        .'4f46460ef2585a94fab058c7c6a1247ca2cfb15f4ab9d713ddd05f6e3ed30324be1060abd9568695458020356edd7a4d5de967'
        .'bc272e44899a7481c9cc4b27430bd9696ea8c5756ca64df8b04f586b1a977b246875ad3914634c08e181d1779cae57986a4a1f';

    /**
     * @throws \Exception
     */
    public function testSimpleRedirect():void
    {
        $middleware = new SecureRedirectMiddleware(bin2hex(random_bytes(16)));

        /** @var RedirectResponse $response */
        $response = $middleware->process(
            FakeServerRequest::getSecureServerRequestWithPath($middleware::DEFAULT_URI_PATH)
                ->withQueryParams([$middleware::DEFAULT_QUERY_PARAMETER => $middleware->encodeUrlJwt(self::TEST_URL)]),
            new FakeRequestHandler()
        );

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertEquals(self::TEST_URL, $response->getRedirectUrl());
    }

    /**
     * @throws \Exception
     */
    public function testJwtEncryption():void
    {
        $middleware = new SecureRedirectMiddleware(bin2hex(random_bytes(16)));
        $jwt = $middleware->encodeUrlJwt(self::TEST_URL);
        self::assertNotEmpty($jwt);
        self::assertNotNull($url = $middleware->decodeUrlJwt($jwt));
        self::assertEquals($url, self::TEST_URL);
    }

    /**
     * @expectedException \CodeInc\RedirectMiddleware\RedirectMiddlewareException
     */
    public function testEmptyJwtKey():void
    {
        new SecureRedirectMiddleware('');
    }

    /**
     * @expectedException \CodeInc\RedirectMiddleware\RedirectMiddlewareException
     * @throws \CodeInc\RedirectMiddleware\RedirectMiddlewareException
     * @throws \Exception
     */
    public function testEncryptionError():void
    {
        $middleware = new SecureRedirectMiddleware(bin2hex(random_bytes(16)));
        $middleware->encodeUrlJwt(hex2bin(self::TEST_DATA));
    }

    /**
     * @expectedException \CodeInc\RedirectMiddleware\RedirectMiddlewareException
     * @throws \CodeInc\RedirectMiddleware\RedirectMiddlewareException
     * @throws \Exception
     */
    public function testDecryptionError():void
    {
        $middleware = new SecureRedirectMiddleware(bin2hex(random_bytes(16)));
        $middleware->decodeUrlJwt(hex2bin(self::TEST_DATA));
    }
}