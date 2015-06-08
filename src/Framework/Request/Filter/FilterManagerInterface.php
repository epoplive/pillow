<?php
/**
 * FilterManagerInterface.php
 * Author: Brett Thomas <brett.thomas@gmail.com>
 */
namespace Framework\Request\Filter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Class FilterManager
 *
 * @package Framework\Filter
 */
interface FilterManagerInterface
{
    /**
     * @param FilterInterface $filter
     */
    public function addFilter(FilterInterface $filter, $position = null);

    public function insertFilterAfter(FilterInterface $search, FilterInterface $filter);

    public function insertFilterBefore(FilterInterface $search, FilterInterface $filter);

    /**
     * @param Request $request
     */
    public function filterRequest(Request $request);

    /**
     * @param Response $response
     */
    public function filterResponse(Response $response);

    /**
     * @return FilterChain
     */
    public function getFilterChain();

    /**
     * @param FilterChain $filterChain
     */
    public function setFilterChain($filterChain);
}