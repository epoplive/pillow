<?php
/**
 * AbstractBaseTemplateView.php
 * Author: Brett Thomas <brett.thomas@gmail.com>
 */

namespace Framework\View\TemplateView;

use Framework\Controller\FrontController;


/**
 * Class AbstractBaseTemplateView
 *
 * @package Framework\View\TemplateView
 */
abstract class AbstractBaseTemplateView implements TemplateViewinterface
{
    /** @var  Array $input */
    protected $input;
    /** @var  string $template */
    protected $template;
    /** @var  string $templateHash */
    protected $templateHash;
    /** @var  string $templateFile */
    protected $templateFile;
    /** @var  Array $config */
    protected $config;

    /**
     * AbstractBaseTemplateView constructor.
     */
    public function __construct(Array $config = null)
    {
        $this->config = $config ?: null;
        if(is_array($config) && count($config) > 0){
            foreach($this->config as $key => $value){
                if(property_exists($this, $key)){
                    $this->{$key} = $value;
                }
            }
        }
    }


    /**
     * return the view input
     * @return Array
     */
    public function getInput() {
        return $this->input;
    }

    /**
     * @param Array $input
     */
    public function setInput($input = null){
        $this->input = $input;
    }

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

    protected function loadTemplateFromFile(){
        if(!file_exists(FrontController::getRootPath().$this->getTemplateFile())){
            throw new \Exception("Template file {$this->getTemplatePath()} not found!");
        }
        $this->template = file_get_contents($this->getTemplatePath());
    }

    protected function getTemplatePath() {
        return rtrim(FrontController::getRootPath(), DIRECTORY_SEPARATOR)."/".ltrim($this->getTemplateFile(), DIRECTORY_SEPARATOR);
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->templateHash = md5($template);
        $this->template = $template;
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

    /**
     * Render the view into a string and return for output
     *
     * @param null|Array $input
     * @return string
     */
    abstract public function render(Array $input = null);

    protected function renderScoped($file, Array $data = null){
        $scope = function() {
            extract( func_get_arg(1) );
            if(!is_file(func_get_arg(0))){
                throw new \Exception("Template file ".func_get_arg(0)." not found!");
            }
            return require func_get_arg(0);
        };
        return $scope($file, (array)$data);
    }
}