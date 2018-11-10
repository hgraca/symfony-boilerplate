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

namespace Acme\App\Test\TestCase\Presentation\Console\Component;

use Acme\App\Presentation\Console\Component\HelloWorldCommand;
use Acme\App\Test\Framework\AbstractConsoleTest;

/**
 * @medium
 */
final class HelloWorldCommandIntegrationTest extends AbstractConsoleTest
{
    protected function setUp(): void
    {
        $this->skipIfSttyNotAvailable();
        parent::setUp();
    }

    /**
     * @test
     */
    public function writes_hello_world(): void
    {
        $output = $this->execute(HelloWorldCommand::getDefaultName());

        self::assertContains(HelloWorldCommand::MESSAGE, $output);
        self::assertNotContains('error', $output, '', true);
        self::assertNotContains('exception', $output, '', true);
    }
}
