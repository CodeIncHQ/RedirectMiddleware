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
// Time:     13:42
// Project:  RedirectMiddleware
//
declare(strict_types=1);
namespace CodeInc\RedirectMiddleware;

/**
 * Class RedirectResponse
 *
 * @uses \CodeInc\Psr7Responses\RedirectResponse
 * @package CodeInc\RedirectMiddleware
 * @author  Joan Fabrégat <joan@codeinc.fr>
 */
class RedirectResponse extends \CodeInc\Psr7Responses\RedirectResponse
{
    /**
     * @var string
     */
    private $redirectUrl;

    /**
     * RedirectResponse constructor.
     *
     * @param string $redirectUrl
     * @param int $status
     * @param array $headers
     * @param null $body
     * @param string $version
     * @param null|string $reason
     */
    public function __construct(string $redirectUrl, int $status = 302, array $headers = [], $body = null,
        string $version = '1.1', ?string $reason = null)
    {
        $this->redirectUrl = $redirectUrl;
        parent::__construct($redirectUrl, $status, $headers, $body, $version, $reason);
    }

    /**
     * @return string
     */
    public function getRedirectUrl():string
    {
        return $this->redirectUrl;
    }
}