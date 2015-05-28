<?php
/**
 * FilterTrait.php
 * Author: Brett Thomas <brett.thomas@gmail.com>
 */

namespace Framework\Request\Filter;


use Symfony\Component\HttpFoundation\Request;

trait FilterManagerTrait
{
    /** @var  FilterChain $filterChain */
    protected $filterChain;

    /**
     * @param FilterInterface $filter
     */
    public function addFilter(FilterInterface $filter){
        $this->filterChain->addFilter($filter);
    }

    public function insertFilterAfter(FilterInterface $search, FilterInterface $filter){
        foreach($this->filterChain->getFilters() as $key => $compareFilter){
            if($compareFilter === $search){
                $this->filterChain->setFilters(array_splice($this->filterChain, $key + 1, 0, $filter));
                return $key + 1;
            }
        }
        return false;
    }

    public function insertFilterBefore(FilterInterface $search, FilterInterface $filter){
        foreach($this->filterChain->getFilters() as $key => $compareFilter){
            if($compareFilter === $search){
                $this->filterChain->setFilters(array_splice($this->filterChain, $key, 0, $filter));
                return $key;
            }
        }
        return false;
    }

    /**
     * @param Request $request
     */
    public function filterRequest(Request $request){
        $this->filterChain->execute($request);
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