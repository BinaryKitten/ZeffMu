<?php

namespace ZeffMu;

use Zend\Mvc\Controller\AbstractController;
use Zend\Mvc\MvcEvent;
use Closure;

/**
 * Description of ClosureController
 *
 * @author Kat
 */
class ClosureController extends AbstractController
{
    /**
     * The closure we are wrapping
     * @var Closure $closure
     */
    protected $closure = null;

    public function __construct(Closure $closure)
    {
        $this->closure = $closure;
    }

    public function onDispatch(MvcEvent $e)
    {
        $routeMatch     = $e->getRouteMatch();
        $application    = $e->getApplication();
        $request        = $e->getRequest();
        $response       = $application->getResponse();

        $closure        = $this->closure;
        if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
            $closure->bindTo($this);
        }
        return $closure($routeMatch->getParams(), $request, $response);
    }
}
