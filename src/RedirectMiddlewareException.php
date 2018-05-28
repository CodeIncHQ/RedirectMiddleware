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
// Time:     13:56
// Project:  RedirectMiddleware
//
declare(strict_types=1);
namespace CodeInc\RedirectMiddleware;
use Throwable;


/**
 * Class RedirectMiddlewareException
 *
 * @package CodeInc\RedirectMiddleware
 * @author  Joan Fabrégat <joan@codeinc.fr>
 */
class RedirectMiddlewareException extends \Exception
{
    /**
     * @var RedirectMiddleware
     */
    private $redirectMiddleware;

    /**
     * RedirectMiddlewareException constructor.
     *
     * @param string $message
     * @param RedirectMiddleware $redirectMiddleware
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(string $message, RedirectMiddleware $redirectMiddleware,
        int $code = 0, Throwable $previous = null)
    {
        $this->redirectMiddleware = $redirectMiddleware;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return RedirectMiddleware
     */
    public function getRedirectMiddleware():RedirectMiddleware
    {
        return $this->redirectMiddleware;
    }
}