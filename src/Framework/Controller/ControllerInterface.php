<?php
/**
 * Created by PhpStorm.
 * User: bthomas
 * Date: 5/20/15
 * Time: 3:23 PM
 */
namespace Framework\Controller;

use Symfony\Component\HttpFoundation\Request;

interface ControllerInterface
{
    /**
     * @return Request
     */
    public function getRequest();

    /**
     * @param Request $request
     */
    public function setRequest($request);
}