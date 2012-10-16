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

use Zend\Mvc\Application;
use Zend\Mvc\Router\Http\TreeRouteStack;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\Mvc\Exception\InvalidArgumentException;
use Zend\ServiceManager\ServiceManager;

/**
 * Zend Framework 2 based micro-framework application
 *
 * @license MIT
 * @author  Marco Pivetta <ocramius@gmail.com>
 * @author  Kathryn Reeve <kathryn@binarykitten.com>
 */
class App extends Application
{
    /**
     * @var TreeRouteStack
     */
    protected $router;

    /**
     * @var \Zend\View\HelperPluginManager
     */
    protected $viewHelper;

    /**
     * @var \Zend\Mvc\Controller\PluginManager
     */
    protected $controllerPlugin;

    /**
     * @param string|\Zend\Mvc\Router\RouteInterface $route
     * @param callable $controller
     */
    public function route($route, $controller)
    {
        if (!isset($this->router)) {
            $this->router = $this->getServiceManager()->get('Router');
        }

        $this->router->addRoute(
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
     * Used to display a custom error page.
     *
     * @param callable $controller
     */
    public function error($controller)
    {
        if (!is_callable($controller)) {
            throw new InvalidArgumentException(sprintf(
                'The argument $controller must be callable, %s given.',
                gettype($controller)
            ));
        }

        $this->getMvcEvent()->setParam('error-controller', $controller);

        return $this;
    }

    /**
     * {@inheritDoc}
     * @return self
     */
    public static function init($config = null)
    {
        $arrayKeys = array('modules', 'module_listener_options', 'service_manager');
        foreach ($arrayKeys as $arrayKey) {
            if (!isset($config[$arrayKey])) {
                $config[$arrayKey] = array();
            }
        }

        $serviceManager = new ServiceManager(new ServiceManagerConfig($config['service_manager']));
        $serviceManager->setService('ApplicationConfig', $config);
        $serviceManager->get('ModuleManager')->loadModules();

        $override = $serviceManager->getAllowOverride();
        $serviceManager->setAllowOverride(true);

        /**
         * This can be removed once https://github.com/zendframework/zf2/pull/2778 was merged.
         */
        $config = $serviceManager->get('Config');
        if (!isset($config['view_manager'])) {
            $config['view_manager'] = array();
            $serviceManager->setService('Config', $config);
        }

        $app    = new self($serviceManager->get('Config'), $serviceManager);
        $router = TreeRouteStack::factory(array());
        $serviceManager->setService('Application', $app);
        $serviceManager->setService('Router', $router);
        $serviceManager->setService('HttpRouter', $router);
        $serviceManager->setInvokableClass('DispatchListener', 'ZeffMu\Dispatcher');

        $serviceManager->setAllowOverride($override);

        return $app->bootstrap();
    }

    /**
     * Retrieves a service from the service manager.
     *
     * @param  string $service
     * @return mixed
     */
    public function getService($service)
    {
        return $this->getServiceManager()->get($service);
    }

    /**
     * Retrieves the ViewHelperManager from the service manager.
     *
     * @return PluginProxy
     */
    public function getViewHelper()
    {
        if (!isset($this->viewHelper)) {
            $pluginProxy = new PluginProxy();
            $viewHelper  = $this->getServiceManager()->get('ViewHelperManager');

            $this->viewHelper = $pluginProxy->setPluginManager($viewHelper);
        }

        return $this->viewHelper;
    }

    /**
     * Retrieves the ControllerPluginManager from the service manager.
     *
     * @return \Zend\Mvc\Controller\PluginManager
     */
    public function getControllerPlugin()
    {
        if (!isset($this->controllerPlugin)) {
            $pluginProxy      = new PluginProxy();
            $controllerPlugin = $this->getServiceManager()->get('ControllerPluginManager');

            $this->controllerPlugin = $pluginProxy->setPluginManager($controllerPlugin);
        }

        return $this->controllerPlugin;
    }

    /**
     * Run the application
     *
     * @return string
     */
    public function __invoke()
    {
        $this->run();
    }
}
