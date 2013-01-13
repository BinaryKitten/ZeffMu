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

if (is_readable(__DIR__ . '/../vendor/autoload.php')) {
    $loader = require __DIR__ . '/../vendor/autoload.php';
} else {
    if (!is_readable(__DIR__ . '/../../../autoload.php')) {
        throw new RuntimeException('Please run composer installation!');
    }

    $loader = require __DIR__ . '/../../../autoload.php';
}

$loader->add('ZeffMuTest\\', __DIR__);
$loader->add('ZeffMuTestAsset\\', __DIR__);

if (is_readable(__DIR__ . DIRECTORY_SEPARATOR . 'TestConfiguration.php')) {
    $config =  __DIR__ . DIRECTORY_SEPARATOR . 'TestConfiguration.php';
} else {
    $config = __DIR__ . DIRECTORY_SEPARATOR . 'TestConfiguration.php.dist';
}
