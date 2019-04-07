<?php

namespace Symfony\Component\Debug\Tests\Fixtures;

class DirectlyImplementsSerializable implements \Serializable
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
