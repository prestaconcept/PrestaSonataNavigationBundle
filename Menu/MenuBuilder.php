<?php
/**
 * This file is part of the PrestaSonataNavigationBundle.
 *
 * (c) PrestaConcept <http://www.prestaconcept.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Presta\SonataNavigationBundle\Menu;

use Presta\SonataNavigationBundle\Event\ConfigureMenuEvent;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Builder for administration main navigation
 *
 * @author Nicolas Bastien <nbastien@prestaconcept.net>
 */
class MenuBuilder extends ContainerAware
{
    /**
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var array
     */
    protected $itemsConfiguration = array();

    /**
     * @var boolean
     */
    protected $withDescription;

    /**
     * @param SecurityContextInterface $securityContext
     */
    public function setSecurityContext($securityContext)
    {
        $this->securityContext = $securityContext;
    }

    /**
     * @param TranslatorInterface $translator
     */
    public function setTranslator($translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function setEventDispatcher($eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param boolean $withDescription
     */
    public function setWithDescription($withDescription)
    {
        $this->withDescription = $withDescription;
    }

    /**
     * @see SecurityContext::isGranted()
     */
    public function isGranted($attributes, $object = null)
    {
        return $this->securityContext->isGranted($attributes, $object );
    }

    /**
     * Translate a message
     *
     * @param  string $message
     * @return string
     */
    protected function trans($message, array $parameters = array(), $domain = 'SonataNavigationBundle')
    {
        return $this->translator->trans($message, $parameters, $domain);
    }

    /**
     * Build administration main navigation
     *
     * @param  FactoryInterface $factory
     * @return ItemInterface    $menu
     */
    public function build(FactoryInterface $factory)
    {
        $menu = $factory->createItem('root');
        //Add default class for twitter bootstrap menu
        $menu->setChildrenAttribute('class', 'nav');

        foreach ($this->itemsConfiguration as $itemConfiguration) {
            if (count($itemConfiguration['roles']) && $this->isGranted($itemConfiguration['roles']) == false) {
                continue;
            }
            $hasChildren = (isset($itemConfiguration['children']) && count($itemConfiguration['children']));

            $itemName = $itemConfiguration['name'];
            if ($hasChildren) {
                $item = $menu->addChild($itemName, array('uri' => '#'));
            } else {
                $item = $menu->addChild($itemName, array('route' => $itemConfiguration['route']));
            }

            $class = 'nav-menu nav-menu-' . $itemName;
            $item->setExtra('safe_label', true);
            if ($hasChildren) {
                //Handle dropdown item
                $class .=  ' dropdown-toggle';
                $item->setAttribute('class', 'dropdown');
                $item->setLinkAttribute('data-toggle', 'dropdown');
                $item->setChildrenAttribute('class', 'dropdown-menu');

                foreach ($itemConfiguration['children'] as $subItemName => $subItemConfiguration) {
                    if (count($subItemConfiguration['roles'])
                        && $this->isGranted($subItemConfiguration['roles']) == false) {
                        continue;
                    }
                    $subItem = $item->addChild(
                        $subItemName,
                        array('route' => $subItemConfiguration['route'])
                    );
                    $subItem->setExtra('safe_label', true);
                    $subItem->setLabel($this->getSubItemLabel($itemName, $subItemName, $this->withDescription));
                    $subItem->setLinkAttribute('class', 'nav-submenu nav-submenu-' . $itemName . '-' . $subItemName);
                }
            }

            $item->setLabel($this->getItemLabel($itemName, $hasChildren));
            $item->setLinkAttribute('class', $class);
        }

        //Menu is already build by configuration
        //Add a custom event for extended needs
        $this->eventDispatcher->dispatch(ConfigureMenuEvent::CONFIGURE, new ConfigureMenuEvent($factory, $menu));

        return $menu;
    }

    /**
     * Add item : used by configuration
     *
     * @param string $itemName
     * @param array  $itemConfiguration
     */
    public function addItem($itemName, array $itemConfiguration)
    {
        $itemConfiguration['name']  = $itemName;
        $this->itemsConfiguration[] = $itemConfiguration;
    }

    /**
     * Returns item label, made to be easily overridden
     *
     * @param  string  $itemName
     * @param  boolean $hasChildren
     * @return string
     */
    protected function getItemLabel($itemName, $hasChildren)
    {
        $label = $this->trans('navigation.' . $itemName . '.label');

        if ($hasChildren) {
            $label .= ' <span class="caret"></span>';
        }

        return $label;
    }

    /**
     * Returns sub item label, made to be easily overridden
     *
     * @param  string  $itemName
     * @param  string  $subItemName
     * @param  boolean $withDescription
     * @return string
     */
    public function getSubItemLabel($itemName, $subItemName, $withDescription)
    {
        $subItemLabel = $this->trans('navigation.' . $itemName . '.' . $subItemName . '.label');

        if ($withDescription) {
            $subItemLabel .= '<p>';
            $subItemLabel .= $this->trans('navigation.' . $itemName . '.' . $subItemName . '.description');
            $subItemLabel .= '</p>';
        }

        return $subItemLabel;
    }
}
