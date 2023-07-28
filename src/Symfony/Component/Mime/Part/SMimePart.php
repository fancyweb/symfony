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

use Symfony\Component\Mime\Header\Headers;

/**
 * @author Sebastiaan Stok <s.stok@rollerscapes.net>
 */
class SMimePart extends AbstractPart
{
    /**
     * @internal
     * @deprecated since Symfony 6.4, to be removed in 7.0
     */
    protected Headers $_headers;

    private iterable|string $body;
    private string $type;
    private string $subtype;
    private array $parameters;

    public function __construct(iterable|string $body, string $type, string $subtype, array $parameters)
    {
        parent::__construct();

        $this->body = $body;
        $this->type = $type;
        $this->subtype = $subtype;
        $this->parameters = $parameters;
    }

    public function getMediaType(): string
    {
        return $this->type;
    }

    public function getMediaSubtype(): string
    {
        return $this->subtype;
    }

    public function bodyToString(): string
    {
        if (\is_string($this->body)) {
            return $this->body;
        }

        $body = '';
        foreach ($this->body as $chunk) {
            $body .= $chunk;
        }
        $this->body = $body;

        return $body;
    }

    public function bodyToIterable(): iterable
    {
        if (\is_string($this->body)) {
            yield $this->body;

            return;
        }

        $body = '';
        foreach ($this->body as $chunk) {
            $body .= $chunk;
            yield $chunk;
        }
        $this->body = $body;
    }

    public function getPreparedHeaders(): Headers
    {
        $headers = clone parent::getHeaders();

        $headers->setHeaderBody('Parameterized', 'Content-Type', $this->getMediaType().'/'.$this->getMediaSubtype());

        foreach ($this->parameters as $name => $value) {
            $headers->setHeaderParameter('Content-Type', $name, $value);
        }

        return $headers;
    }

    /**
     * @deprecated since Symfony 6.4, to be removed in 7.0
     */
    public function __sleep(): array
    {
        trigger_deprecation('symfony/mime', '6.4', 'The "%s()" method is deprecated and will be removed in Symfony 7.0.', __METHOD__);

        // convert iterables to strings for serialization
        if (is_iterable($this->body)) {
            $this->body = $this->bodyToString();
        }

        $this->_headers = $this->getHeaders();

        return ['_headers', 'body', 'type', 'subtype', 'parameters'];
    }

    /**
     * @deprecated since Symfony 6.4, to be removed in 7.0
     */
    public function __wakeup(): void
    {
        trigger_deprecation('symfony/mime', '6.4', 'The "%s()" method is deprecated and will be removed in Symfony 7.0.', __METHOD__);

        $r = new \ReflectionProperty(AbstractPart::class, 'headers');
        $r->setValue($this, $this->_headers);
        unset($this->_headers);
    }

    public function __serialize(): array
    {
        // convert iterables to strings for serialization
        if (is_iterable($this->body)) {
            $this->body = $this->bodyToString();
        }

        return [...parent::__serialize(), 'body' => $this->body, 'type' => $this->type, 'subtype' => $this->subtype, 'parameters' => $this->parameters];
    }

    public function __unserialize(array $data): void
    {
        parent::__unserialize($data);
        $this->body = $data['body'] ?? $data["\x00Symfony\Component\Mime\Part\SMimePart\x00body"];
        $this->type = $data['type'] ?? $data["\x00Symfony\Component\Mime\Part\SMimePart\x00type"];
        $this->subtype = $data['subtype'] ?? $data["\x00Symfony\Component\Mime\Part\SMimePart\x00subtype"];
        $this->parameters = $data['parameters'] ?? $data["\x00Symfony\Component\Mime\Part\SMimePart\x00parameters"];
    }
}
