<?php
/**
 * Created by PhpStorm.
 * User: bthomas
 * Date: 5/24/15
 * Time: 12:59 PM
 */

namespace Framework\Request\Filter;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class JSONRequestFilter implements FilterInterface
{
    /**
     * @param Request $request
     * @return bool
     */
    public function filterRequest(Request $request)
    {
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), false);
            if (!is_array($data)) {
                // interesting choice here on how to deal with items sent as a single object
                // the issue is that we don't have a way to tell if we were sent a single object or multiple when we
                // cast to array, making us force our endpoint to accept only one type or employ some odd type of object detection
//                $data = [(array)$data];
                $data = (array)$data;
            }
            $request->request->replace(is_array($data) ? $data : []);
        }
    }

    /**
     * @param Response $response
     * @return bool
     */
    public function filterResponse(Response $response)
    {
        // TODO: Implement filterResponse() method.
    }


}