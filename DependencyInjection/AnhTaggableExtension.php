<?php

namespace Anh\TaggableBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class AnhTaggableExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * Add mapping for Tag and Tagging entities from doctrine extension.
     */
    public function prepend(ContainerBuilder $container)
    {
        $container->prependExtensionConfig('doctrine', array(
            'orm' => array(
                'mappings' => array(
                    'anh_taggable' => array(
                        'type' => 'annotation',
                        'prefix' => 'Anh\Taggable\Entity',
                        'dir' => '%kernel.root_dir%/../vendor/anh/doctrine-extensions-taggable/lib/Anh/Taggable/Entity',
                        'alias' => 'AnhTaggable',
                        'is_bundle' => false
                    )
                )
            )
        ));

        $container->prependExtensionConfig('assetic', array(
            'assets' => array(
                'anh_taggable_css' => array(
                    'inputs' => array(
                        'bundles/anhtaggable/components/tag-it/css/jquery.tagit.css',
                        'bundles/anhtaggable/style.css'
                    )
                ),
                'anh_taggable_js' => array(
                    'inputs' => array(
                        'bundles/anhtaggable/components/tag-it/js/tag-it.js',
                        'bundles/anhtaggable/init.js'
                    )
                )
            )
        ));

        $container->prependExtensionConfig('sp_bower', array(
            'assetic' => array(
                'enabled' => false
            ),
            'bundles' => array(
                'AnhTaggableBundle' => null
            )
        ));

        $container->prependExtensionConfig('anh_doctrine_resource', array(
            'resources' => array(
                'anh_taggable.tag' => array(
                    'model' => '%anh_taggable.entity.tag.class%',
                    'driver' => 'orm',
                ),
                'anh_taggable.tagging' => array(
                    'model' => '%anh_taggable.entity.tagging.class%',
                    'driver' => 'orm',
                ),
            )
        ));
    }
}
