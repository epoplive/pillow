<?php
/**
 * AbstractBaseFileTemplateView.php
 * Author: Brett Thomas <brett.thomas@gmail.com>
 */

namespace Framework\View\TemplateView;


use Framework\Controller\FrontController;

abstract class AbstractBaseFileTemplateView extends AbstractBaseTemplateView
{
    /** @var  string $templateHash */
    protected $templateHash;
    /** @var  string $templateFile */
    protected $templateFile;

    /**
     * @return string
     */
    public function getTemplate()
    {
        if($this->getTemplateFile()){
            if(!$this->templateHash || $this->templateHash !== md5($this->template)){
                $this->loadTemplateFromFile();
            }
        }
        return $this->template;
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->templateHash = md5($template);
        $this->template = $template;
    }

    protected function loadTemplateFromFile(){
        if(!file_exists($this->getTemplatePath())){
            throw new \Exception("Template file {$this->getTemplatePath()} not found!");
        }
        $this->template = file_get_contents($this->getTemplatePath());
    }

    protected function getTemplatePath() {
        return rtrim(FrontController::getRootPath(), DIRECTORY_SEPARATOR)."/".ltrim($this->getTemplateFile(), DIRECTORY_SEPARATOR);
    }

    protected function renderScoped($file, $data = null){
        $scope = function($input, $templateFile) {
            ob_start();
            if(is_array($input)){
                extract( $input );
            }

            if(!$realPath = stream_resolve_include_path($templateFile)){
                throw new \Exception("Template file {$templateFile} not found!", 500);
            }
            require $realPath;
            $contents = ob_get_contents(); // get contents of buffer
            ob_end_clean();
            return $contents;
        };
        return $scope($file, (array)$data);
    }

    /**
     * @return string
     */
    public function getTemplateFile()
    {
        return $this->templateFile;
    }

    /**
     * @param string $templateFile
     */
    public function setTemplateFile($templateFile)
    {
        $this->templateFile = $templateFile;
    }
}