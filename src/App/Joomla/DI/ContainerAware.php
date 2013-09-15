<?php


namespace App\Joomla\DI;

use Joomla\DI\Container;
use Joomla\DI\ContainerAwareInterface;

abstract class ContainerAware implements ContainerAwareInterface
{
    /**
     * DI Container object.
     *
     * @var Container
     */
    protected $container = null;

    /**
     * Sets the Container associated with this Controller.
     *
     * @param ContainerInterface $container A ContainerInterface instance
     */
    public function setContainer(Container $container = null)
    {
        $this->container = $container;
    }
    
    /**
     * function getContainer
     */
    public function getContainer()
    {
        return $this->container;
    }
}