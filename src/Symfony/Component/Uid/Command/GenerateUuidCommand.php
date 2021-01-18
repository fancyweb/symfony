<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Uid\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Uid\Factory\UuidFactory;
use Symfony\Component\Uid\Uuid;

class GenerateUuidCommand extends Command
{
    protected static $defaultName = 'uuid:generate';
    protected static $defaultDescription = 'Generates a UUID';

    private $factory;

    public function __construct(UuidFactory $factory = null)
    {
        $this->factory = $factory ?? new UuidFactory();

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDefinition([
                new InputOption('time-based', null, InputOption::VALUE_REQUIRED, 'The timestamp, to generate a time-based UUID: a parsable date/time string. It must be greater than or equals to the UUID epoch (1582-10-15 00:00:00)'),
                new InputOption('node', null, InputOption::VALUE_REQUIRED, 'The UUID whose node part should be used as the node of the generated UUID'),
                new InputOption('name-based', null, InputOption::VALUE_REQUIRED, 'The name, to generate a name-based UUID'),
                new InputOption('namespace', null, InputOption::VALUE_REQUIRED, 'The UUID to use at the namespace for named-based UUIDs'),
                new InputOption('random-based', null, InputOption::VALUE_NONE, 'To generate a random-based UUID'),
                new InputOption('count', 'c', InputOption::VALUE_REQUIRED, 'The number of UUID to generate', 1),
                new InputOption('format', 'f', InputOption::VALUE_REQUIRED, 'The UUID output format: rfc4122, base58 or base32', 'rfc4122'),
            ])
            ->setDescription(self::$defaultDescription)
            ->setHelp(<<<'EOF'
The <info>%command.name%</info> generates a UUID.

    <info>php %command.full_name%</info>

To generate a time-based UUID:

    <info>php %command.full_name% --time-based=now</info>

To specify a time-based UUID's node:

    <info>php %command.full_name% --time-based=@1613480254 --node=fb3502dc-137e-4849-8886-ac90d07f64a7</info>

To generate a name-based UUID:

    <info>php %command.full_name% --name-based=foo</info>

To specify a name-based UUID's namespace:

    <info>php %command.full_name% --name-based=bar --namespace=fb3502dc-137e-4849-8886-ac90d07f64a7</info>

To generate a random-based UUID:

    <info>php %command.full_name% --random-based</info>

To generate several UUIDs:

    <info>php %command.full_name% --count=10</info>

To output a specific format:

    <info>php %command.full_name% --format=base58</info>
EOF
            )
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output);

        $time = $input->getOption('time-based');
        $node = $input->getOption('node');
        $name = $input->getOption('name-based');
        $namespace = $input->getOption('namespace');
        $random = $input->getOption('random-based');

        switch (true) {
            case !$time && !$node && !$name && !$namespace && !$random:
                $create = [$this->factory, 'create'];

                break;
            case $time && !$name && !$namespace && !$random:
                if ($node) {
                    try {
                        $node = Uuid::fromString($node);
                    } catch (\InvalidArgumentException $e) {
                        $io->error(sprintf('Invalid node "%s". %s', $node, $e->getMessage()));

                        return 1;
                    }
                }

                try {
                    $time = new \DateTimeImmutable($time);
                } catch (\Exception $e) {
                    $io->error(sprintf('Invalid timestamp "%s". %s', $time, str_replace('DateTimeImmutable::__construct(): ', '', $e->getMessage())));

                    return 2;
                }

                if ($time < new \DateTimeImmutable('@-12219292800')) {
                    $io->error(sprintf('Invalid timestamp "%s". It must be greater than or equals to the UUID epoch (1582-10-15 00:00:00).', $input->getOption('time-based')));

                    return 3;
                }

                $create = function () use ($node, $time): Uuid { return $this->factory->timeBased($node)->create($time); };

                break;
            case $name && !$time && !$node && !$random:
                if ($namespace) {
                    try {
                        $namespace = Uuid::fromString($namespace);
                    } catch (\InvalidArgumentException $e) {
                        $io->error(sprintf('Invalid namespace "%s". %s', $namespace, $e->getMessage()));

                        return 4;
                    }
                } else {
                    $refl = new \ReflectionProperty($this->factory, 'nameBasedNamespace');
                    $refl->setAccessible(true);
                    if (null === $refl->getValue($this->factory)) {
                        $io->error('Missing namespace. Use the "--namespace" option or configure a default namespace in the underlying factory.');

                        return 5;
                    }
                }

                $create = function () use ($namespace, $name): Uuid { return $this->factory->nameBased($namespace)->create($name); };

                break;
            case $random && !$time && !$node && !$name && !$namespace:
                $create = [$this->factory->randomBased(), 'create'];

                break;
            default:
                $io->error('Invalid combination of options.');

                return 6;
        }

        switch ($input->getOption('format')) {
            case 'rfc4122':
                $format = 'strval';

                break;
            case 'base58':
                $format = static function (Uuid $uuid): string { return $uuid->toBase58(); };

                break;
            case 'base32':
                $format = static function (Uuid $uuid): string { return $uuid->toBase32(); };

                break;
            default:
                $io->error(sprintf('Invalid format "%s". Supported formats are rfc4122, base58 and base32.', $input->getOption('format')));

                return 7;
        }

        $count = (int) $input->getOption('count');
        for ($i = 0; $i < $count; ++$i) {
            $io->writeln($format($create()));
        }

        return 0;
    }
}
