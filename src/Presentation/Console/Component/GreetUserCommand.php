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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use function count;

final class GreetUserCommand extends AbstractCommandStopwatchDecorator
{
    public const ARG_FIRST_NAME = 'first_name';
    public const ARG_SECOND_NAME = 'second_name';
    public const ARG_LAST_NAMES = 'last_names';
    public const OPT_ITERATIONS = 'iterations';
    private const NAME = 'boilerplate:greet:user';

    /**
     * To make your command lazily loaded, configure the $defaultName static property,
     * so it will be instantiated only when the command is actually called.
     *
     * @var string
     */
    protected static $defaultName = self::NAME;

    protected function configure(): void
    {
        $this
            ->setDescription('Greets the use, using all names and repeating several times.')
            ->addArgument(self::ARG_FIRST_NAME, InputArgument::REQUIRED, 'Who do you want to greet?')
            ->addArgument(self::ARG_SECOND_NAME, InputArgument::OPTIONAL, 'Your last name?')
            ->addArgument(
                self::ARG_LAST_NAMES,
                InputArgument::IS_ARRAY | InputArgument::OPTIONAL,
                'Who do you want to greet (separate multiple names with a space)?'
            )
            ->addOption(
                self::OPT_ITERATIONS,
                'i',
                InputOption::VALUE_REQUIRED,
                'How many times should the message be printed?',
                1
            );
    }

    protected function executeUseCase(InputInterface $input, OutputInterface $output): void
    {
        $text = 'Hi ' . $input->getArgument(self::ARG_FIRST_NAME);

        $secondName = $input->getArgument(self::ARG_SECOND_NAME);
        if ($secondName) {
            $text .= ' ' . $secondName;
        }

        $lastNames = $input->getArgument(self::ARG_LAST_NAMES);
        if (count($lastNames) > 0) {
            $text .= ' ' . implode(' ', $lastNames);
        }

        for ($i = 0; $i < $input->getOption(self::OPT_ITERATIONS); ++$i) {
            $output->writeln($text . '!');
        }
    }
}
