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

use Zend\Mvc\Application as ZfApplication;
use Zend\Mvc\Router\Http\Part as PartRoute;
use Zend\Stdlib\ArrayUtils;
use Zend\Mvc\Router\RouteInterface;
use ZeffMu\ClosureController;

/**
 * Zend Framework 2 based micro-framework application
 *
 * @license MIT
 * @author  Marco Pivetta <ocramius@gmail.com>
 * @author  Kathryn Reeve <kathryn@binarykitten.com>
 */
class App extends ZfApplication
{
    /**
     * @param string|RouteInterface $route
     * @param Closure|String $controller
     *
     * @return $this
     */
    public function route($route, $controller)
    {
        $sm     = $this->getServiceManager();
        /* @var $cpm \Zend\Mvc\Controller\ControllerManager */
        $cpm    = $sm->get('ControllerLoader');

        if ($controller instanceof \Closure) {
            $wrappedController = new ClosureController($controller);
            $controller = "ZeffMu\\Controllers\\" .  md5($route);
            $cpm->setFactory(
                $controller,
                function () use ($wrappedController) {
                    return $wrappedController;
                }
            );
        }

        $sm
            ->get('Router')
            ->addRoute(
                $route,
                array(
                    'type' => 'Zend\Mvc\Router\Http\Segment',
                    'options' => array(
                        'route' => $route,
                        'defaults' => array(
                            'controller' => $controller,
                        ),
                    ),
                )
            );

        return $this;
    }

    /**
     * {@inheritDoc}
     * @return self
     */
    public static function init($configuration = array())
    {

        $defaults = array(
            'module_listener_options' => array(),
            'modules' => array(),
            'service_manager' => array(),
        );

        $configuration = ArrayUtils::merge($defaults, $configuration);

        $configuration['modules'][] = 'ZeffMu';

        return parent::init($configuration);
    }

    /**
     * @param $service
     *
     * @return array|object
     */
    public function getService($service)
    {
        return $this->getServiceManager()->get($service);
    }

    /**
     * Launch the app
     */
    public function __invoke()
    {
        $this->run();
    }
}
