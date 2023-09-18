<?php 

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response ;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class ErrorController extends AbstractController
{
    public function show (FlattenException $exception,Environment $env):Response
    {
        $view = "bundles/TwigBundle/Exception/error.{$exception->getStatusCode()}.html.twig";

        if(!$env->getloader()->exists($view)){
            $view ="bundles/TwigBundle/Exception/error.html.twig";
        }
        return $this->render($view);
    }
}