<?php
/**
 * AnnotationValidatorTrait.php
 * Author: Brett Thomas <brett.thomas@gmail.com>
 */

namespace Entity\Validator;


use Framework\Controller\FrontController;
use Symfony\Component\Validator\Validation;

trait AnnotationValidatorTrait
{
    private $helper;
    public function validate(){
        $this->helper = include(FrontController::getRootPath()."/src/cli_config.php");
        $validator = Validation::createValidatorBuilder()
            ->enableAnnotationMapping()
            ->getValidator();
        $violations = $validator->validate($this);
        return $violations;
    }
}