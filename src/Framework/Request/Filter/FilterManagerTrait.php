<?php
/**
 * FilterTrait.php
 * Author: Brett Thomas <brett.thomas@gmail.com>
 */

namespace Framework\Request\Filter;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

trait FilterManagerTrait
{
    /** @var  FilterChain $filterChain */
    protected $filterChain;

    /**
     * @param FilterInterface $filter
     */
    public function addFilter(FilterInterface $filter, $position = null){
        if($position !== null){
            $filters = $this->filterChain->getFilters();
            array_splice($filters, (int)$position, 0, [$filter]);
            $this->filterChain->setFilters($filters);
        } else {
            $this->filterChain->addFilter($filter);
        }

    }

    public function insertFilterAfter(FilterInterface $search, FilterInterface $filter){
        foreach($this->filterChain->getFilters() as $key => $compareFilter){
            if($compareFilter === $search){
                $filters = $this->filterChain->getFilters();
                if(($key + 1) >= count($filters)){
                    $filters = array_splice($filters, $key + 1, 0, [$filter]);
                } else {
                    $filters[] = $filter;
                }
                $this->filterChain->setFilters($filters);
                return $key + 1;
            }
        }
        return false;
    }

    public function insertFilterBefore(FilterInterface $search, FilterInterface $filter){
        foreach($this->filterChain->getFilters() as $key => $compareFilter){
            if($compareFilter === $search){
                $filters = $this->filterChain->getFilters();
                $this->filterChain->setFilters(array_splice($filters, $key, 0, [$filter]));
                return $key;
            }
        }
        return false;
    }

    /**
     * @param Request $request
     */
    public function filterRequest(Request $request){
        $this->filterChain->filterRequest($request);
    }

    /**
     * @param Response $response
     */
    public function filterResponse(Response $response){
        $this->filterChain->filterResponse($response);
    }

    /**
     * @return FilterChain
     */
    public function getFilterChain()
    {
        return $this->filterChain;
    }

    /**
     * @param FilterChain $filterChain
     */
    public function setFilterChain($filterChain)
    {
        $this->filterChain = $filterChain;
    }
}