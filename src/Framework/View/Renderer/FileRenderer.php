<?php
/**
 * FileRenderer.php.
 * Author: brett.thomas@gmail.com
 * Date: 6/21/15
 * Time: 1:18 PM
 */

namespace Framework\View\Renderer;

class FileRenderer implements RendererInterface
{

    /**
     * Render the view into a string and return for output
     *
     * @return \Closure
     */
    public function getRenderer()
    {
        return function($templateFile, $input) {
            ob_start();
            if(is_array($input)){
                extract( $input );
            }

            if(!$realPath = stream_resolve_include_path($templateFile)){
                throw new \Exception("Template file {$templateFile} not found! (include path: ".get_include_path().")", 500);
            }
            require $realPath;
            $contents = ob_get_contents(); // get contents of buffer
            ob_end_clean();
            return $contents;
        };
    }


}