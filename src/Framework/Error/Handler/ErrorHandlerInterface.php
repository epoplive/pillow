<?php
/**
 * ErrorHandlerInterface.php
 * Author: Brett Thomas <brett.thomas@gmail.com>
 */

namespace Framework\Error\Handler;


interface ErrorHandlerInterface
{
    /**
     * @param \Exception $e
     * @return mixed
     */
    public function handleException(\Exception $e);
}