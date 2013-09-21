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
     * __construct description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  __constructReturn
     *
     * @since  1.0
     */
    public function __construct(Container $container = null)
    {
        $this->container = $container;
    }
    
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