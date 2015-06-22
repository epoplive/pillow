<?php
/**
 * FileBasedTemplate.php.
 * Author: brett.thomas@gmail.com
 * Date: 6/21/15
 * Time: 1:18 PM
 */

namespace Framework\View\Template;


class FileBasedTemplate extends AbstractTemplate implements TemplateInterface
{
    protected $filePath;

    public function setContent($content)
    {
        if(!$realPath = stream_resolve_include_path($content)){
            throw new \Exception("File not found ({$content})", 500);
        }
        $this->filePath = $realPath;
    }

    public function getContent()
    {
        return file_get_contents($this->filePath);
    }
}