<?php

namespace Symfony\Component\DependencyInjection\Tests\Fixtures;

use Psr\Container\ContainerInterface as PsrContainerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final class AutowiredContainer
{
    public function __construct(PsrContainerInterface $container)
    {
    }

    public function setContainer(ContainerInterface $container): void
    {
    }
}
