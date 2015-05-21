<?php
namespace Framework\Request\Filter;


use Symfony\Component\HttpFoundation\Request;

/**
 * Interface FilterInterface
 *
 * @package Framework\Filter
 */
interface FilterInterface
{
    /**
     * @param Request $request
     */
    public function execute(Request $request);
}