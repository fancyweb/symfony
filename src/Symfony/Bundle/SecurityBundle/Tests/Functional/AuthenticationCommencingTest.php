<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\SecurityBundle\Tests\Functional;

class AuthenticationCommencingTest extends WebTestCase
{
    /**
     * @expectedDeprecation The "Twig\Profiler\Profile" class implements the broken "\Serializable" interface. It is discouraged to do so. It is going to be deprecated and removed in future PHP versions.
     */
    public function testAuthenticationIsCommencingIfAccessDeniedExceptionIsWrapped()
    {
        $client = $this->createClient(['test_case' => 'StandardFormLogin', 'root_config' => 'config.yml']);

        $client->request('GET', '/secure-but-not-covered-by-access-control');
        $this->assertRedirect($client->getResponse(), '/login');
    }
}
