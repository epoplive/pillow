<?php
/**
 * Created by PhpStorm.
 * User: bthomas
 * Date: 5/20/15
 * Time: 3:34 PM
 */

namespace Framework\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\Annotation;

trait RequestResponseTrait
{
    /**
     * @var  Request $request
     * @Annotation\Exclude
     */
    protected $request;

    /**
     * @var  Response $response
     * @Annotation\Exclude
     */
    protected $response;

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest($request)
    {
        $this->request = $request;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param Response $response
     */
    public function setResponse($response)
    {
        $this->response = $response;
    }


}