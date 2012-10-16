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

use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Allows the usage of plugins like in the view layer of a normal Zend\Mvc application.
 */
class PluginProxy
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $pluginManager;

    /**
     * Set a service locator (plugin manager).
     *
     * @param  ServiceLocatorInterface $pluginManager
     * @return self
     */
    public function setPluginManager(ServiceLocatorInterface $pluginManager)
    {
        $this->pluginManager = $pluginManager;

        return $this;
    }

    /**
     * Get the service locator (plugin manager).
     *
     * @return ServiceLocatorInterface
     */
    public function getPluginManager()
    {
        return $this->pluginManager;
    }

    /**
     * Get plugin instance
     *
     * @param  string     $name Name of plugin to return
     * @param  null|array $options Options to pass to plugin constructor (if not already instantiated)
     * @return object
     */
    public function plugin($name, array $options = null)
    {
        return $this->getPluginManager()->get($name, $options);
    }

    /**
     * Overloading: proxy to helpers or plugins
     *
     * Proxies to the attached plugin manager to retrieve, return, and potentially
     * execute plugins.
     *
     * * If the plugin does not define __invoke, it will be returned
     * * If the plugin does define __invoke, it will be called as a functor
     *
     * @param  string $method
     * @param  array  $argv
     * @return mixed
     */
    public function __call($method, $argv)
    {
        $helper = $this->plugin($method);

        if (is_callable($helper)) {
            return call_user_func_array($helper, $argv);
        }

        return $helper;
    }
}
