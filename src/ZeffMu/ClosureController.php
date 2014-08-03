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

use Zend\Mvc\Controller\AbstractController;
use Zend\Mvc\MvcEvent;
use Zend\View\Model\ViewModel;
use Closure;

/**
 * Description of ClosureController
 *
 * @author Kathryn Reeve <Kathryn@BinaryKitten.com>
 */
class ClosureController extends AbstractController
{

    /**
     * The closure we are wrapping
     * @var Closure $closure
     */
    protected $closure = null;

    /**
     * Constructor
     * @param Closure $closure
     */
    public function __construct(Closure $closure)
    {
        $this->closure = $closure;
    }

    /**
     *
     * @param  \Zend\Mvc\MvcEvent $e
     * @return misc
     */
    public function onDispatch(MvcEvent $e)
    {
        $routeMatch = $e->getRouteMatch();
        $application = $e->getApplication();
        $request = $e->getRequest();
        $response = $application->getResponse();

        $closure = $this->closure;
        // if php > 5.4
        if (PHP_VERSION_ID > 50400) {
            $closure = $closure->bindTo($this);
        }

        $result = $closure(
            $routeMatch->getParams(), $request, $response
        );

        if (is_array($result)) {
            $result = new ViewModel($result);
            $result->setTemplate($template);
        } elseif (!($result instanceof ViewModel)) {
            $response->setContent($result);

            return $response;
        }

        $e->setResult($result);

        return $result;
    }

}
