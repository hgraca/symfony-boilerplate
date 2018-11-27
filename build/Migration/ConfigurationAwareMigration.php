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

namespace Acme\App\Build\Migration;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class ConfigurationAwareMigration extends AbstractMigration implements ContainerAwareInterface
{
    public const DOMAIN_COM = 'app.acme.com';

    public const ENVIRONMENT_PRODUCTION = 'production';
    public const ENVIRONMENT_STAGING = 'staging';

    /**
     * @var ContainerInterface
     */
    protected $container;

    public function setContainer(ContainerInterface $container = null): void
    {
        $this->container = $container;
    }

    protected function getDomain(): string
    {
        return $this->container->getParameter('environment.domain');
    }

    protected function getEnvironment(): string
    {
        return $this->container->getParameter('environment.name');
    }

    protected function isProduction(): bool
    {
        return mb_strstr($this->getEnvironment(), 'production') !== false;
    }

    protected function isTesting(): bool
    {
        return mb_strstr($this->getEnvironment(), 'testing') !== false;
    }
}
