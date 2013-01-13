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

namespace ZeffMuTest;

use PHPUnit_Framework_TestCase;
use ZeffMu\App;

use Zend\Console\Console;
use Zend\Http\Request;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;

/**
 * Functional test demonstrating the features of a ZeffMu application
 *
 * @license MIT
 * @author  Marco Pivetta <ocramius@gmail.com>
 */
class AppFunctionalTest extends PHPUnit_Framework_TestCase
{
    public function testSimpleClosureController()
    {
        Console::overrideIsConsole(false);

        $app      = App::init();
        $test     = $this;
        $appRequest  = new Request();

        $appRequest->setUri('http://localhost/test/blah');
        $app->getMvcEvent()->setRequest($appRequest);

        $app->route(
            '/test/:param1',
            function (array $params, RequestInterface $req, ResponseInterface $res)
            use ($test, $appRequest, &$response) {
                $test->assertArrayHasKey('param1', $params);
                $test->assertSame($appRequest, $req);

                $response = $res;

                return 'Hello world!';
            }
        );

        ob_start();
        $appResponse = $app->run();
        $responseString = ob_get_contents();
        ob_end_clean();

        $this->assertSame('Hello world!', $responseString);
        $this->assertSame($response, $appResponse);
    }
}
