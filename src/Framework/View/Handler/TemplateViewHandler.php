<?php
/**
 * TemplateViewHandler.php
 * Author: Brett Thomas <brett.thomas@gmail.com>
 */

namespace Framework\View\Handler;


use Framework\View\TemplateView\TemplateViewInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TemplateViewHandler implements ViewHandlerInterface
{
    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function transform(Request $request, Response $response){
        $templateClass = $request->attributes->get("route")->getViewClass();
        $template = new $templateClass($request->attributes->get("route")->toArray());
        if(!$template instanceof TemplateViewInterface){
            throw new \Exception("Invalid template class.  Class must be an instance of ".TemplateViewInterface::class, 400);
        }
        $content = $template->render($request->attributes->get("viewData"));
        $response->setContent($content);
        $response->setStatusCode(200);
        return $response;
    }
}