<?php
/**
 * Created by PhpStorm.
 * User: bthomas
 * Date: 5/19/15
 * Time: 10:58 PM
 */

namespace Framework\Controller;


use Symfony\Component\HttpFoundation\Request;

class AbstractBaseController implements ControllerInterface
{
    use RequestResponseTrait;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

}