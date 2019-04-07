<?php

namespace Symfony\Component\Debug\Tests\Fixtures;

class ImplementsAnInterfaceThatExtendsSerializable implements InterfaceExtendingSerializable
{
    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return 'foo';
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
    }
}
