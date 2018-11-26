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

use PHPUnit\Framework\TestCase;

/**
 * KernelTestCase is the base class for tests needing a Kernel.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
abstract class KernelTestCase extends TestCase
{
    /**
     * @var string
     */
    protected static $class;

    /**
     * @var CliTestKernel
     */
    protected static $kernel;

    /**
     * Clean up Kernel usage in this test.
     */
    protected function tearDown(): void
    {
        static::ensureKernelShutdown();
    }

    protected static function bootKernel(array $options = []): CliTestKernel
    {
        static::ensureKernelShutdown();

        static::$kernel = static::createKernel($options);
        static::$kernel->boot();

        return static::$kernel;
    }

    /**
     * Shuts the kernel down if it was used in the test.
     */
    protected static function ensureKernelShutdown(): void
    {
        if (static::$kernel !== null) {
            static::$kernel->shutdown();
        }
    }

    /**
     * Creates a Kernel.
     *
     * Available options:AddUserCommandIntegrationTest.php
     *
     *  * environment
     *  * debug
     */
    protected static function createKernel(array $options = []): CliTestKernel
    {
        if (static::$class === null) {
            static::$class = static::getKernelClass();
        }

        if (isset($options['environment'])) {
            $env = $options['environment'];
        } elseif (isset($_ENV['APP_ENV'])) {
            $env = $_ENV['APP_ENV'];
        } else {
            $env = 'test';
        }

        if (isset($options['debug'])) {
            $debug = $options['debug'];
        } elseif (isset($_ENV['APP_DEBUG'])) {
            $debug = $_ENV['APP_DEBUG'];
        } else {
            $debug = true;
        }

        return new static::$class($env, (bool) $debug);
    }

    /**
     * Attempts to guess the kernel location.
     *
     * When the Kernel is located, the file is required.
     *
     * @throws \RuntimeException
     *
     * @return string The Kernel class name
     */
    protected static function getKernelClass(): string
    {
        if (!isset($_ENV['KERNEL_CLASS'])) {
            throw new \RuntimeException(
                'The environment variable KERNEL_CLASS needs to be set. You can set it in PHPUnit config file, phpunit.xml.'
            );
        }
        $class = $_ENV['KERNEL_CLASS'];
        if (!class_exists($class)) {
            throw new \RuntimeException(
                sprintf(
                    'Class "%s" doesn\'t exist or cannot be autoloaded.'
                    . 'Check that the KERNEL_CLASS value in phpunit.xml matches the fully-qualified class name of'
                    . 'your Kernel or override the %s::createKernel() method.',
                    $class,
                    static::class
                )
            );
        }

        return $class;
    }
}
