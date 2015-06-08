<?php
namespace Framework\Request\Filter;

use Symfony\Component\HttpFoundation\Request;

/**
 * Class FilterManager
 *
 * @package Framework\Filter
 */
class FilterManager implements FilterManagerInterface
{
    use FilterManagerTrait;

    /**
     * FilterManager constructor.
     *
     * @param mixed $target
     */
    public function __construct($target)
    {
        $this->filterChain = new FilterChain($target);
    }
}