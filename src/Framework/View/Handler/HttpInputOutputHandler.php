<?php
/**
 * HttpInputOutputHandler.php.
 * Author: brett.thomas@gmail.com
 * Date: 6/21/15
 * Time: 6:50 PM
 */

namespace Framework\View\Handler;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HttpInputOutputHandler implements InputOutputHandlerInterface
{
    /** @var  Request $request */
    protected $input;
    /** @var  Response $response */
    protected $output;

    public function transform($input, $output)
    {
        $this->_transform($input, $output);
    }

    /**
     * Internal version of the transform function that allows benefit from error handing of typehints
     * @param Request $request
     * @param Response $response
     */
    protected function _transform(Request $request, Response $response)
    {

    }

    /**
     * @param \Exception $e
     * @return mixed
     * @throws \Exception
     */
    public function handleException(\Exception $e)
    {
        // TODO: Implement handleException() method.
    }

}