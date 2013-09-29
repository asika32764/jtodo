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
	 * Get the DI container.
	 *
	 * @return  Container
	 *
	 * @since   1.0
	 *
	 * @throws  \UnexpectedValueException May be thrown if the container has not been set.
	 */
    public function setContainer(Container $container = null)
    {
        $this->container = $container;
    }
    
    /**
	 * Set the DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @since   1.0
	 */
    public function getContainer()
    {
        return $this->container;
    }
}