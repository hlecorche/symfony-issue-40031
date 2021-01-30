<?php

declare(strict_types=1);

/*
 * This file is part of the MyBundle package.
 *
 * (c) E-commit <contact@e-commit.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Issue\MyBundle\Tests\Functional\App;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Issue\MyBundle\IssueMyBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel implements CompilerPassInterface
{
    use MicroKernelTrait;

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $loader->load($this->getProjectDir().'/config/framework.yaml');
        $loader->load($this->getProjectDir().'/config/doctrine.yaml');
        $loader->load($this->getProjectDir().'/config/services.yaml');
    }

    public function process(ContainerBuilder $container): void
    {
    }

    public function registerBundles()
    {
        return [
            new DoctrineBundle(),
            new FrameworkBundle(),
            new IssueMyBundle(),
        ];
    }

    public function getProjectDir()
    {
        return __DIR__;
    }
}
