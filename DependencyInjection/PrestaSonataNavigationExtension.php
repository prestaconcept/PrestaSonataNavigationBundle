<?php
/**
 * This file is part of the PrestaSonataNavigationBundle.
 *
 * (c) PrestaConcept <http://www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Presta\SonataNavigationBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

/**
 * @author Nicolas Bastien <nbastien@prestaconcept.net>
 */
class PrestaSonataNavigationExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if (isset($config['menu']) && is_array($config['menu'])) {
            $this->buildMenu($container, $config['menu']);
        }
    }

    /**
     * Build Knp menu for administration
     *
     * @param ContainerBuilder $container
     * @param array            $menuConfiguration
     */
    protected function buildMenu(ContainerBuilder $container, array $menuConfiguration)
    {
        $menuBuilderDefinition = $container->getDefinition('presta_sonata_navigation.menu_builder');
        $menuBuilderDefinition->addMethodCall('setWithDescription', array($menuConfiguration['with_description']));
        foreach ($menuConfiguration['items'] as $itemName => $itemConfiguration) {
            $menuBuilderDefinition->addMethodCall('addItem', array($itemName, $itemConfiguration));
        }
    }
}
