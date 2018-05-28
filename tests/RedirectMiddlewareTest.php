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
// Time:     14:04
// Project:  RedirectMiddleware
//
declare(strict_types=1);
namespace CodeInc\RedirectMiddleware\Tests;
use CodeInc\MiddlewareTestKit\BlankResponse;
use CodeInc\MiddlewareTestKit\FakeRequestHandler;
use CodeInc\MiddlewareTestKit\FakeServerRequest;
use CodeInc\RedirectMiddleware\RedirectMiddleware;
use CodeInc\RedirectMiddleware\RedirectResponse;
use PHPUnit\Framework\TestCase;


/**
 * Class RedirectMiddlewareTest
 *
 * @uses RedirectMiddleware
 * @package CodeInc\RedirectMiddleware\Tests
 * @author  Joan Fabrégat <joan@codeinc.fr>
 */
class RedirectMiddlewareTest extends TestCase
{
    private const TEST_URL = 'https://www.example.com';

    public function testSimpleRedirect():void
    {
        $middleware = new RedirectMiddleware();

        /** @var RedirectResponse $response */
        $response = $middleware->process(
            FakeServerRequest::getSecureServerRequestWithPath($middleware::DEFAULT_URI_PATH)
                ->withQueryParams([$middleware::DEFAULT_QUERY_PARAMETER => self::TEST_URL]),
            new FakeRequestHandler()
        );

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertEquals(self::TEST_URL, $response->getRedirectUrl());
    }

    public function testRedirectWithCustomQueryParam():void
    {
        $customQueryParam = uniqid('redirect-url-');
        $middleware = new RedirectMiddleware(RedirectMiddleware::DEFAULT_URI_PATH, $customQueryParam);

        /** @var RedirectResponse $response */
        $response = $middleware->process(
            FakeServerRequest::getSecureServerRequestWithPath(RedirectMiddleware::DEFAULT_URI_PATH)
                ->withQueryParams([$customQueryParam => self::TEST_URL]),
            new FakeRequestHandler()
        );

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertEquals(self::TEST_URL, $response->getRedirectUrl());
    }

    public function testRedirectWithCustomPath():void
    {
        $customPath = '/'.uniqid('a-custom-redirect-path-');
        $middleware = new RedirectMiddleware($customPath);

        /** @var RedirectResponse $response */
        $response = $middleware->process(
            FakeServerRequest::getSecureServerRequestWithPath($customPath)
                ->withQueryParams([$middleware::DEFAULT_QUERY_PARAMETER => self::TEST_URL]),
            new FakeRequestHandler()
        );

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertEquals(self::TEST_URL, $response->getRedirectUrl());
    }

    public function testRedirectWithCustomPathAndCustomQueryParam():void
    {
        $customPath = '/'.uniqid('a-custom-redirect-path-');
        $customQueryParam = uniqid('redirect-url-');
        $middleware = new RedirectMiddleware($customPath, $customQueryParam);

        /** @var RedirectResponse $response */
        $response = $middleware->process(
            FakeServerRequest::getSecureServerRequestWithPath($customPath)
                ->withQueryParams([$customQueryParam => self::TEST_URL]),
            new FakeRequestHandler()
        );

        self::assertInstanceOf(RedirectResponse::class, $response);
        self::assertEquals(self::TEST_URL, $response->getRedirectUrl());
    }

    public function testNoRedirectWithUrlParam():void
    {
        $middleware = new RedirectMiddleware();

        /** @var RedirectResponse $response */
        $response = $middleware->process(
            FakeServerRequest::getSecureServerRequestWithPath("/a-non-redirect-path")
                ->withQueryParams([$middleware::DEFAULT_QUERY_PARAMETER => self::TEST_URL]),
            new FakeRequestHandler()
        );
        self::assertInstanceOf(BlankResponse::class, $response);
    }

    public function testNoRedirectWithoutUrlParam():void
    {
        $middleware = new RedirectMiddleware();

        /** @var RedirectResponse $response */
        $response = $middleware->process(
            FakeServerRequest::getSecureServerRequestWithPath("/a-non-redirect-path"),
            new FakeRequestHandler()
        );
        self::assertInstanceOf(BlankResponse::class, $response);
    }
}