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
            if(!$this->templateHash !== md5($this->template)){
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
        if(!file_exists(FrontController::getRootPath().$this->getTemplateFile())){
            throw new \Exception("Template file {$this->getTemplatePath()} not found!");
        }
        $this->template = file_get_contents($this->getTemplatePath());
    }

    protected function getTemplatePath() {
        return rtrim(FrontController::getRootPath(), DIRECTORY_SEPARATOR)."/".ltrim($this->getTemplateFile(), DIRECTORY_SEPARATOR);
    }

    protected function renderScoped($file, $data = null){
        $scope = function() {
            ob_start();
            $input = func_get_arg(1);
            $templateFile = func_get_arg(0);
            if(is_array($input)){
                extract( $input );
            }
            if(!is_file($templateFile)){
                throw new \Exception("Template file {$templateFile} not found!");
            }
            require $templateFile;
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