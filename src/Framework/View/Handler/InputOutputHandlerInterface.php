<?php
/**
 * InputOutputHandlerInterface.php.
 * Author: brett.thomas@gmail.com
 * Date: 6/21/15
 * Time: 6:47 PM
 */

namespace Framework\View\Handler;


use Framework\View\ViewInterface;

interface InputOutputHandlerInterface
{

    /**
     * @param $input
     * @return $output
     */
    public function transform($input);

    /**
     * @return ViewInterface
     */
    public function getView();

    /**
     * @param ViewInterface $view
     */
    public function setView($view);
}