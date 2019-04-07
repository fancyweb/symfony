<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Bundle\FrameworkBundle\Tests\Functional;

class FragmentTest extends WebTestCase
{
    /**
     * @dataProvider getConfigs
     *
     * @group legacy
     * @expectedDeprecation The "Twig\Profiler\Profile" class implements the broken "\Serializable" interface. It is discouraged to do so. It is going to be deprecated and removed in future PHP versions.
     */
    public function testFragment($insulate)
    {
        $client = $this->createClient(['test_case' => 'Fragment', 'root_config' => 'config.yml', 'debug' => true]);
        if ($insulate) {
            $client->insulate();
        }

        $client->request('GET', '/fragment_home');

        $this->assertEquals(<<<TXT
bar txt
--
html
--
es
--
fr
TXT
            , $client->getResponse()->getContent());
    }

    public function getConfigs()
    {
        return [
            [false],
            [true],
        ];
    }
}
