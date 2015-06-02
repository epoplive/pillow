<?php
namespace Framework\Request\Filter;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Interface FilterInterface
 * This is an example of the remote interface design pattern
 *
 * @package Framework\Filter
 */
interface FilterInterface
{
    /**
     * @param Request $request
     * @return bool
     */
    public function filterRequest(Request $request);

    /**
     * @param Response $response
     * @return bool
     */
    public function filterResponse(Response $response);
}