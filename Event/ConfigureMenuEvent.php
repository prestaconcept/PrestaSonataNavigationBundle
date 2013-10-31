<?php
/**
 * This file is part of the PrestaSonataNavigationBundle.
 *
 * (c) PrestaConcept <http://www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Presta\SonataNavigationBundle\Event;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Event configuration for Administration main navigation construction
 *
 * @package    PrestaSonata
 * @subpackage AdminBundle
 * @author     Nicolas Bastien nbastien@prestaconcept.net
 */
class ConfigureMenuEvent extends Event
{
    const CONFIGURE = 'presta_sonata_navigation.menu_configure';

    private $factory;
    private $menu;

    /**
     * @param \Knp\Menu\FactoryInterface $factory
     * @param \Knp\Menu\ItemInterface    $menu
     */
    public function __construct(FactoryInterface $factory, ItemInterface $menu)
    {
        $this->factory = $factory;
        $this->menu = $menu;
    }

    /**
     * @return \Knp\Menu\FactoryInterface
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * @return \Knp\Menu\ItemInterface
     */
    public function getMenu()
    {
        return $this->menu;
    }
}
