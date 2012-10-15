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
use Zend\ServiceManager\Exception\InvalidServiceNameException;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\ServiceManager\ServiceManager;
use Zend\Stdlib\ArrayUtils;
use Zend\Mvc\MvcEvent;
use Zend\Mvc\InjectApplicationEventInterface;
use Zend\Mvc\DispatchListener as ZfDispatchListener;

/**
 * Closure dispatch listener - dispatches any requests where a route match includes
 * a closure as 'controller' parameter
 */
class DispatchListener extends ZfDispatchListener
{
    /**
     * {@inheritDoc}
     */
    public function onDispatch(MvcEvent $e)
    {
        $routeMatch       = $e->getRouteMatch();
        $controller       = $routeMatch->getParam('controller', null);
        $application      = $e->getApplication();
        $events           = $application->getEventManager();

        if (null === $controller) {
            $return = $this->marshallControllerNotFoundEvent(
                Application::ERROR_CONTROLLER_NOT_FOUND,
                $controller,
                new ServiceNotFoundException(),
                $e,
                $application
            );
            return $this->complete($return, $e);
        }

        if (!is_callable($controller)) {
            $return = $this->marshallControllerNotFoundEvent(
                Application::ERROR_CONTROLLER_INVALID,
                gettype($controller),
                new InvalidServiceNameException(),
                $e,
                $application
            );
            return $this->complete($return, $e);
        }

        $request  = $e->getRequest();
        $response = $application->getResponse();

        if ($controller instanceof InjectApplicationEventInterface) {
            $controller->setEvent($e);
        }

        try {
            $return = $controller($routeMatch->getParams(), $request, $response);
        } catch (\Exception $ex) {
            $e
                ->setError(Application::ERROR_EXCEPTION)
                ->setController($controller)
                ->setControllerClass(get_class($controller))
                ->setParam('exception', $ex);
            $results = $events->trigger(MvcEvent::EVENT_DISPATCH_ERROR, $e);
            $return = $results->last();

            if (! $return) {
                $return = $e->getResult();
            }
        }

        return $this->complete($return, $e);
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

        if (!is_object($return)) {
            if (ArrayUtils::hasStringKeys($return)) {
                $return = new ArrayObject($return, ArrayObject::ARRAY_AS_PROPS);
            }
        }

        $event->setResult($return);

        return $return;
    }
}
