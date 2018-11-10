<?php

declare(strict_types=1);

/*
 * This file is part of my Symfony boilerplate,
 * following the Explicit Architecture principles.
 *
 * @link https://herbertograca.com/2017/11/16/explicit-architecture-01-ddd-hexagonal-onion-clean-cqrs-how-i-put-it-all-together
 * @link https://herbertograca.com/2018/07/07/more-than-concentric-layers/
 *
 * (c) Herberto Graça
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Acme\App\Presentation\Console\Component;

use Acme\App\Presentation\Console\AbstractCommandStopwatchDecorator;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class HelloWorldCommand extends AbstractCommandStopwatchDecorator
{
    public const MESSAGE = 'Hello world!';
    private const NAME = 'boilerplate:hello:world';

    /**
     * To make your command lazily loaded, configure the $defaultName static property,
     * so it will be instantiated only when the command is actually called.
     *
     * @var string
     */
    protected static $defaultName = self::NAME;

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setDescription('Writes "hello world" on the screen. ')
            ->setHelp($this->getCommandHelp());
    }

    /**
     * @throws Exception
     */
    protected function executeUseCase(InputInterface $input, OutputInterface $output): void
    {
        $this->io->success(self::MESSAGE);
    }

    private function getCommandHelp(): string
    {
        return <<<'HELP'
The <info>%command.name%</info> writes "hello world" on the screen. 
It's used as an example and to test that the boilerplate is including the commands 
and running them when compiled into a phar.
HELP;
    }
}
