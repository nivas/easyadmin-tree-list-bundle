<?php

namespace Nivas\Bundle\EasyAdminTreeListBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class EasyAdminTreeListExtension extends Extension implements PrependExtensionInterface
{

    /**
     * in case our bundle needs config, we load it here
     *
     * @param array $configs
     * @param ContainerBuilder $container
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        // $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../../config'));
        // $loader->load('services.yaml');
    }

    /**
     * injecting twig template paths so we can override bundle from bundle
     */
    public function prepend(ContainerBuilder $container)
    {
        // recommended to use FileLocator here 
        $thirdPartyBundlesViewFileLocator = (new FileLocator(__DIR__ . '/../../templates/bundles'));

        $container->loadFromExtension('twig', [
            'paths' => [
                $thirdPartyBundlesViewFileLocator->locate('EasyAdminBundle') => 'EasyAdmin',
 //             '%kernel.project_dir%/vendor/nivas/easyadmin-tree-list-bundle/templates' => 'EasyAdmin', // You use the namespace you found earlier here. Discard the `@` symbol.                
                // $thirdPartyBundlesViewFileLocator->locate('JMoseCommandSchedulerBundle') => 'JMoseCommandScheduler',
                // $thirdPartyBundlesViewFileLocator->locate('TwigBundle') => 'Twig',
            ],
        ]);
    }    
}