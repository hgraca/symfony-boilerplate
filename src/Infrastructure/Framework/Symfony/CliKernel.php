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

namespace Acme\App\Infrastructure\Framework\Symfony;

use Exception;
use ReflectionObject;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\ClosureLoader;
use Symfony\Component\DependencyInjection\Loader\DirectoryLoader;
use Symfony\Component\DependencyInjection\Loader\GlobFileLoader;
use Symfony\Component\DependencyInjection\Loader\IniFileLoader;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use function dirname;

/**
 * The CliKernel can not handle bundles because they are ALWAYS tied to the HttpKernel component because that
 * is where the BundleInterface and Abstract Bundle class are. Ie, the monolog bundle will add the HttpKernel to
 * the project dependencies.
 * It can't also cache the container, because it would also need a lot of classes from the HttpKernel component.
 *
 * Symfony should have a separate component for these base classes, so we would be able to build new kernels that
 * would use bundles and/or caching for the container, without using the HttpKernel component.
 *
 * We can, however, load the bundles configuration using their extensions
 *
 * @see https://symfony.com/doc/current/components/dependency_injection/compilation.html
 * Which means that we can copy the bundles extension file into the project and use the underlying library.
 *
 * So this CliKernel responsibility is to setup the container
 * @see https://symfony.com/doc/current/components/dependency_injection.html
 */
class CliKernel
{
    private const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    /**
     * @var ContainerBuilder
     */
    protected $container;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var string
     */
    private $environment;

    /**
     * @var bool
     */
    protected $isBooted = false;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $projectDir;

    /**
     * @var bool
     */
    protected $resetServices = false;

    public function __construct(string $environment)
    {
        $this->environment = $environment;
        $this->rootDir = $this->getRootDir();
        $this->name = $this->getName();
    }

    /**
     * @throws \Exception
     */
    public function boot(): void
    {
        if ($this->isBooted) {
            if ($this->resetServices) {
                if ($this->container->has('services_resetter')) {
                    $this->container->get('services_resetter')->reset();
                }
                $this->resetServices = false;
            }

            return;
        }

        $this->container = $this->getContainerBuilder();
        $this->addServicesConfig($this->getContainerLoader($this->container));
        $this->addCompilerPasses($this->container);
        $this->container->compile();
        $this->container->set('kernel', $this);

        $this->isBooted = true;
    }

    public function runApplication(InputInterface $input = null, OutputInterface $output = null): void
    {
        $this->boot();
        $cliApplication = $this->container->get(CliApplication::class);
        $cliApplication->setKernel($this);
        $cliApplication->run($input, $output);
    }

    private function getContainerLoader(ContainerBuilder $containerBuilder): DelegatingLoader
    {
        $locator = new FileLocator();
        $resolver = new LoaderResolver(
            [
                new XmlFileLoader($containerBuilder, $locator),
                new YamlFileLoader($containerBuilder, $locator),
                new IniFileLoader($containerBuilder, $locator),
                new PhpFileLoader($containerBuilder, $locator),
                new GlobFileLoader($containerBuilder, $locator),
                new DirectoryLoader($containerBuilder, $locator),
                new ClosureLoader($containerBuilder),
            ]
        );

        return new DelegatingLoader($resolver);
    }

    /**
     * Gets a new ContainerBuilder instance used to build the service container.
     */
    private function getContainerBuilder(): ContainerBuilder
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->getParameterBag()->add($this->getKernelParameters());

        return $containerBuilder;
    }

    /**
     * @throws Exception
     */
    private function addServicesConfig(LoaderInterface $loader): void
    {
        $confDir = $this->getConfDir();

        $loader->load($confDir . '/services' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/services_' . $this->environment . self::CONFIG_EXTS, 'glob');
    }

    /**
     * The extension point similar to the Bundle::build() method.
     *
     * Use this method to register compiler passes and manipulate the container during the building process.
     */
    private function addCompilerPasses(ContainerBuilder $containerBuilder): void
    {
        /** @var bool[][] $contents */
        $contents = require $this->getConfDir() . '/compiler_pass.php';
        foreach ($contents as $compilerPass => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                $containerBuilder->addCompilerPass(new $compilerPass());
            }
        }
    }

    private function getName(): string
    {
        if ($this->name === null) {
            $this->name = preg_replace('/[^a-zA-Z0-9_]+/', '', basename($this->rootDir));
            if (ctype_digit($this->name[0])) {
                $this->name = '_' . $this->name;
            }
        }

        return $this->name;
    }

    /**
     * Gets the application root dir (path of the project's Kernel class).
     */
    private function getRootDir(): string
    {
        if ($this->rootDir === null) {
            $r = new ReflectionObject($this);
            $this->rootDir = dirname($r->getFileName());
        }

        return $this->rootDir;
    }

    private function getConfDir(): string
    {
        return $this->getProjectDir() . '/config';
    }

    private function getKernelParameters(): array
    {
        return [
            'kernel.root_dir' => realpath($this->rootDir) ?: $this->rootDir,
            'kernel.project_dir' => realpath($this->getProjectDir()) ?: $this->getProjectDir(),
            'kernel.environment' => $this->environment,
            'kernel.name' => $this->name,
            'kernel.container_class' => $this->getContainerClass(),
        ];
    }

    private function getContainerClass(): string
    {
        return Container::class;
    }

    private function getProjectDir(): string
    {
        if ($this->projectDir === null) {
            $r = new \ReflectionObject($this);
            $dir = $appDir = \dirname($r->getFileName());
            while (!file_exists($dir . '/bin/console')) {
                if ($dir === \dirname($dir)) {
                    return $this->projectDir = $appDir;
                }
                $dir = \dirname($dir);
            }
            $this->projectDir = $dir;
        }

        return $this->projectDir;
    }
}
