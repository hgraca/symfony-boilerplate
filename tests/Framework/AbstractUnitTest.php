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

use Acme\App\Test\Framework\Mock\MockTrait;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\TestCase;

/**
 * A unit test will test a method, class or set of classes in isolation from the tools and delivery mechanisms.
 * How isolated the test needs to be, it depends on the situation and how you decide to test the application.
 * However, it is important to note that these tests do not need to boot the application.
 */
abstract class AbstractUnitTest extends TestCase implements AppTestInterface
{
    use AppTestTrait;
    use MockeryPHPUnitIntegration;
    use MockTrait;
}
