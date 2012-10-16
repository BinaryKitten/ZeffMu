<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <https://github.com/BinaryKitten/ZeffMu>.
 */

namespace ZeffMu;

use ArrayObject;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\DispatchListener;
use Zend\Mvc\InjectApplicationEventInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\Stdlib\ArrayUtils;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\Exception\InvalidServiceNameException;

/**
 * Closure dispatch listener - dispatches any requests where a route match includes
 * a closure as 'controller' parameter
 */
class Dispatcher extends DispatchListener
{
    /**
     * {@inheritDoc}
     */
    public function attach(EventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH, array($this, 'onDispatch'));
        $this->listeners[] = $events->attach(MvcEvent::EVENT_DISPATCH_ERROR, array($this, 'onDispatchError'), 100);
    }

    /**
     * Listen to the "dispatch" event
     *
     * @param  MvcEvent $event
     * @return mixed
     */
    public function onDispatch(MvcEvent $event)
    {
        $routeMatch  = $event->getRouteMatch();
        $controller  = $routeMatch->getParam('controller', null);
        $application = $event->getApplication();
        $events      = $application->getEventManager();

        if (null === $controller) {
            $return = $this->marshallControllerNotFoundEvent(
                App::ERROR_CONTROLLER_NOT_FOUND,
                $controller,
                new ServiceNotFoundException(),
                $event,
                $application
            );
            return $this->complete($return, $event);
        }

        if (!is_callable($controller)) {
            $return = $this->marshallControllerNotFoundEvent(
                App::ERROR_CONTROLLER_INVALID,
                gettype($controller),
                new InvalidServiceNameException(),
                $event,
                $application
            );
            return $this->complete($return, $event);
        }

        $request  = $event->getRequest();
        $response = $application->getResponse();

        if ($controller instanceof InjectApplicationEventInterface) {
            $controller->setEvent($event);
        }

        try {
            $return = $controller($routeMatch->getParams(), $request, $response);
        } catch (\Exception $exception) {
            $event->setError(App::ERROR_EXCEPTION)
              ->setController($controller)
              ->setControllerClass(get_class($controller))
              ->setParam('exception', $exception);

            $results = $events->trigger(MvcEvent::EVENT_DISPATCH_ERROR, $event);
            $return  = $results->last();

            if (!$return) {
                $return = $event->getResult();
            }
        }

        return $this->complete($return, $event);
    }

    /**
     * Listen to the "dispatch.error" event
     *
     * @param  MvcEvent $event
     * @return mixed
     */
    public function onDispatchError(MvcEvent $event)
    {
        $controller = $event->getParam('error-controller', null);

        if ($controller === null) {
            return;
        }

        if (!is_callable($controller)) {
            return;
        }

        $event->stopPropagation();

        return $this->complete($controller($event), $event);
    }

    /**
     * {@inheritDoc}
     */
    protected function complete($return, MvcEvent $event)
    {
        if (is_string($return)) {
            $response = $event->getResponse();
            $response->setContent($return);

            return $response;
        }

        return parent::complete($return, $event);
    }
}
