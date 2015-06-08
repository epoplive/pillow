<?php
/**
 * FilterManagerTest.php
 * Author: Brett Thomas <brett.thomas@gmail.com>
 */

namespace pillow\tests\Fixtures;


use Framework\Controller\FrontController;
use Framework\Request\Filter\FilterInterface;
use Framework\Request\Filter\FilterManager;

class FilterManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testAddFilter(){
        $fc = FrontController::getInstance();
        $manager = new FilterManager($fc);
        $this->assertEmpty($manager->getFilterChain()->getFilters());
        $filter = $this->getMock(FilterInterface::class);
        $manager->addFilter($filter);
        $this->assertCount(1, $manager->getFilterChain()->getFilters());
        $fc->destroy();
    }

    public function testInsertFilterBefore()
    {
        $fc = FrontController::getInstance();
        $manager = new FilterManager($fc);
        $this->assertEmpty($manager->getFilterChain()->getFilters());
        $filter = $this->getMock(FilterInterface::class);
        $manager->addFilter($filter);
        $filter2 = $this->getMock(FilterInterface::class);
        $manager->addFilter($filter2);
        $this->assertCount(2, $manager->getFilterChain()->getFilters());

        $filter3 = $this->getMock(FilterInterface::class);
        $manager->insertFilterBefore($filter2, $filter3);
        $this->assertCount(3, $manager->getFilterChain()->getFilters());
        $this->assertSame($filter3, $manager->getFilterChain()->getFilters()[1]);
        $fc->destroy();
    }

    public function testInsertFilterAfter()
    {
        $fc = FrontController::getInstance();
        $manager = new FilterManager($fc);
        $this->assertEmpty($manager->getFilterChain()->getFilters());
        $filter = $this->getMock(FilterInterface::class);
        $manager->addFilter($filter);
        $filter2 = $this->getMock(FilterInterface::class);
        $manager->addFilter($filter2);
        $this->assertCount(2, $manager->getFilterChain()->getFilters());

        $filter3 = $this->getMock(FilterInterface::class);
        $manager->insertFilterAfter($filter, $filter3);
        $this->assertCount(3, $manager->getFilterChain()->getFilters());
        $this->assertSame($filter3, $manager->getFilterChain()->getFilters()[1]);
        $fc->destroy();
    }

//    public function testFilterRequest()
//    {
//
//    }
//
//    public function testFilterResponse()
//    {
//
//    }

    public function testGetSetFilterChain()
    {
        $fc = FrontController::getInstance();
        $manager = new FilterManager($fc);
        $this->assertNull($manager->getFilterChain()->getFilters());
        $fc->destroy();
    }
}
