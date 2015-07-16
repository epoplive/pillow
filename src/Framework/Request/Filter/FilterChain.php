<?php
namespace Framework\Request\Filter;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class FilterChain
 *
 * @package Framework\Filter
 */
class FilterChain
{
    /**
     * @var mixed $target
     */
    private $target;
    /**
     * @var array $filters
     */
    private $filters = [];

    /**
     * @param mixed $target
     * @throws \Exception
     */
    public function __construct($target = null)
    {
        if($target){
            $this->target = $target;
        }
        $this->filters = [];
    }

    /**
     * @param FilterInterface $filter
     */
    public function addFilter(FilterInterface $filter){
        $this->filters[] = $filter;
    }

    /**
     * @param Request $request
     */
    public function filterRequest(Request $request){
        $exception = null;
        foreach($this->filters as $filter){
            /** @var FilterInterface $filter */
            try {
                $filter->filterRequest($request);
                if($this->target){
                    $this->target->filterRequest($request);
                }
            } catch (\Exception $e){
                $exception = new \RuntimeException($e->getMessage(), $e->getCode(), $exception);
            }
        }
    }

    /**
     * @param Response $response
     */
    public function filterResponse(Response $response){
        $exception = null;
        foreach($this->filters as $filter){
            /** @var FilterInterface $filter */
            try {
                $filter->filterResponse($response);
                if($this->target){
                    $this->target->filterResponse($response);
                }
            } catch (\Exception $e){
                $exception = new \RuntimeException($e->getMessage(), $e->getCode(), $exception);
            }
        }
    }

    /**
     * @return mixed
     */
    public function getTarget()
    {
        return $this->target;
    }

    /**
     * @param mixed $target
     * @throws \Exception
     */
    public function setTarget($target)
    {
        if(!is_object($target)){
            throw new \Exception("Target must have a public method callable named execute()");
        } else if(!is_callable([$this->target, "execute"])){
            throw new \Exception("Target does not have a callable named execute!");
        }
        $this->target = $target;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param array $filters
     */
    public function setFilters($filters)
    {
        $this->filters = $filters;
    }
}