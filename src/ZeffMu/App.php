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
use Zend\Mvc\Router\RouteInterface;

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
     * @param callable $controller
     */
    public function route($route, $controller)
    {
        if ($controller instanceof \Closure) {
            $controller = new ClosureController($controller);
        }

        $this
            ->getServiceManager()
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
    public static function init($configuration = null)
    {
        if (null === $configuration) {
            $configuration = array(
                'module_listener_options' => array(),
                'modules' => array(),
                'service_manager' => array(),
            );
        }

        if (!isset($configuration['modules'])) {
            $configuration['modules'] = array();
        }

        $configuration['modules'][] = 'ZeffMu';

        return parent::init($configuration);
    }

    public function getService($service)
    {
        return $this->getServiceManager()->get($service);
    }

    public function __invoke()
    {
        $this->run();
    }
}
