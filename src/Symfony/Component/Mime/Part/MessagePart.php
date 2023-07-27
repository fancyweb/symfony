<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mime\Part;

use Symfony\Component\Mime\Message;
use Symfony\Component\Mime\RawMessage;

/**
 * @final
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class MessagePart extends DataPart
{
    private RawMessage $message;

    public function __construct(RawMessage $message)
    {
        if ($message instanceof Message) {
            $name = $message->getHeaders()->getHeaderBody('Subject').'.eml';
        } else {
            $name = 'email.eml';
        }
        parent::__construct('', $name);

        $this->message = $message;
    }

    public function getMediaType(): string
    {
        return 'message';
    }

    public function getMediaSubtype(): string
    {
        return 'rfc822';
    }

    public function getBody(): string
    {
        return $this->message->toString();
    }

    public function bodyToString(): string
    {
        return $this->getBody();
    }

    public function bodyToIterable(): iterable
    {
        return $this->message->toIterable();
    }

    /**
     * @deprecated since Symfony 6.4, to be removed in 7.0
     */
    public function __sleep(): array
    {
        trigger_deprecation('symfony/mime', '6.4', 'The "%s()" method is deprecated and will be removed in Symfony 7.0.', __METHOD__);

        return ['message'];
    }

    /**
     * @deprecated since Symfony 6.4, to be removed in 7.0
     */
    public function __wakeup(): void
    {
        trigger_deprecation('symfony/mime', '6.4', 'The "%s()" method is deprecated and will be removed in Symfony 7.0.', __METHOD__);

        $this->__construct($this->message);
    }

    public function __serialize(): array
    {
        return ['message' => $this->message];
    }

    public function __unserialize(array $data): void
    {
        $this->__construct($data['message'] ?? $data["\x00Symfony\Component\Mime\Part\MessagePart\x00message"]);
    }
}
