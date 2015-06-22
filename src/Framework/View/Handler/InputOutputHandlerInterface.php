<?php
/**
 * InputOutputHandlerInterface.php.
 * Author: brett.thomas@gmail.com
 * Date: 6/21/15
 * Time: 6:47 PM
 */

namespace Framework\View\Handler;


interface InputOutputHandlerInterface
{

    public function transform($input, $output);

    /**
     * @param \Exception $e
     * @return mixed
     * @throws \Exception
     */
    public function handleException(\Exception $e);
}