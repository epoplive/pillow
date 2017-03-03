<?php
/**
 * ViewInterface.php.
 * Author: brett.thomas@gmail.com
 * Date: 6/21/15
 * Time: 2:18 PM
 */
namespace Framework\View;

interface ViewInterface
{
    /**
     * View constructor.
     */
    public function __construct();

    /**
     * Render a template into a string with optional input data
     *
     * @param $template
     * @param array|null $data
     * @return string
     */
    public function render($template, Array $data = null);

    /**
     * @param \Exception $e
     * @return mixed
     * @throws \Exception
     */
    public function handleException(\Exception $e);

    public function getData();
    public function setData(Array $data = null);
    public function updateData(Array $data);
}