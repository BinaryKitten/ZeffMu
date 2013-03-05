<?php

namespace ZeffMu;

use Zend\Mvc\View\Http\InjectTemplateListener as ZendViewInjectTemplateListener;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;

/**
 * Description of InjectTemplateListener
 *
 * @author k.reeve
 */
class InjectTemplateListener extends ZendViewInjectTemplateListener
{
    public function injectTemplate(MvcEvent $e)
    {
        $model = $e->getResult();
        \Zend\Debug\Debug::dump($model);
        if (!$model instanceof ViewModel) {
            return;
        }

        $template = $model->getTemplate();
        if (empty($template) || $template == 'zeff-mu/closure') {
            $controller = $e->getTarget();
            $model->setTemplate($controller->getControllerName());
        }
        
    }
}