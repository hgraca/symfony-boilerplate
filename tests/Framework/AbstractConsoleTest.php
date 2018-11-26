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

namespace Acme\App\Test\Framework;

use Symfony\Component\Console\Tester\CommandTester;

abstract class AbstractConsoleTest extends AbstractIntegrationTest
{
    protected function execute(string $commandName, array $arguments = []): string
    {
        $commandTester = new CommandTester(
            self::$kernel->getCliCommand($commandName)
        );

        $commandTester->execute(
            [
                'command' => $commandName,
            ] + $arguments,
            [
                'interactive' => false,
            ]
        );

        return $commandTester->getDisplay();
    }

    protected function skipIfSttyNotAvailable(): void
    {
        exec('stty 2>&1', $output, $exitcode);
        $isSttySupported = $exitcode === 0;

        $isWindows = '\\' === \DIRECTORY_SEPARATOR;

        if ($isWindows || !$isSttySupported) {
            $this->markTestSkipped('`stty` is required to test this command.');
        }
    }
}
