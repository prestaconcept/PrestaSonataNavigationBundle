<?php
/**
 * This file is part of the PrestaSonataNavigationBundle
 *
 * (c) PrestaConcept <http://www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Presta\SonataNavigationBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Nicolas Bastien <nbastien@prestaconcept.net>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('presta_sonata_navigation');

        $rootNode
            ->children()
                ->arrayNode('menu')
                    ->children()
                        ->booleanNode('with_description')->defaultValue(true)->end()
                        ->arrayNode('items')
                            ->requiresAtLeastOneElement()
                            ->useAttributeAsKey('name')
                            ->prototype('array')
                                ->children()
                                    ->scalarNode('route')->end()
                                    ->arrayNode('roles')
                                        ->prototype('scalar')->end()
                                    ->end()
                                    ->arrayNode('children')
                                        ->useAttributeAsKey('name')
                                        ->prototype('array')
                                            ->children()
                                                ->scalarNode('route')->end()
                                                ->arrayNode('roles')
                                                    ->prototype('scalar')->end()
                                                ->end()
                                            ->end()
                                        ->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }
}
