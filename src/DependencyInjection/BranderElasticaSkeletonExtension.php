<?php

namespace Brander\Bundle\ElasticaSkeletonBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Extension for BranderElasticaSkeletonBundle.
 *
 * @author Tomfun <tomfun1990@gmail.com>
 */
class BranderElasticaSkeletonExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configDir = __DIR__ . '/../Resources/config';

        $processor = new Processor();
        $config = $processor->processConfiguration(
            new Configuration($this->getAlias()),
            $configs
        );
        $container->setParameter(
            $this->getAlias(),
            $config
        );

        //old style
        $container->setParameter(
            $this->getAlias() . '.jsmodeldir',
            realpath(__DIR__ . '/../Resources/scripts/jsmodel')
        );
        //new style
        $container->setParameter(
            $this->getAlias() . '.' . 'frontend_config', //JsmodelProviderPass::PARAMETER_POSTFIX,
            [
                [
                    'path' => realpath(__DIR__ . '/../Resources/scripts/jsmodel'),
                    'name' => 'brander-elastica-skeleton',
                ],
            ]
        );

        $loader = new YamlFileLoader(
            $container,
            new FileLocator($configDir)
        );
        $loader->load('services.yml');
    }
}
