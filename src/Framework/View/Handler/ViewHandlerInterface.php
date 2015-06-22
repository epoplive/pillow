<?php
/**
 * ViewHandlerInterface.php
 * Author: Brett Thomas <brett.thomas@gmail.com>
 */
namespace Framework\View\Handler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface ViewHandlerInterface
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function transform(Request $request, Response $response);

    public function handleException(\Exception $e);
}