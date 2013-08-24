<?php
/**
 * Part of the Joomla Standard Edition Component Package
 *
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace App\Joomla\Component;

use App\Joomla\Application\Application;
use Joomla\DI\ServiceProviderInterface;
use Joomla\DI\Container;

abstract class AbstractComponent implements ComponentInterface, ServiceProviderInterface
{
    /**
     * Component name.
     *
     * @var string
     * @since 1.0
     */
    protected $name = null ;
    
    /**
	 * @var    Application
	 * @since  1.0
	 */
	protected $application;
    
    /**
     * DI Container object.
     *
     * @var Container
     * @since 1.0
     */
    protected $container = null ;
    
    /**
	 * Constructor.
	 *
	 * @param   TrackerApplication  $application  The application
	 *
	 * @since   1.0
	 */
	public function __construct(Application $application, Container $container)
    {
        $this->application = $application;
		
		$this->register($container);
    }
    
    /**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Container  Returns itself to support chaining.
	 *
	 * @since   1.0
	 */
	public function register(Container $container)
    {
        $container->set($this->getName(), $this);
        
        return $container;
    }
    
    /**
     * function getName
     */
    public function getName()
    {
        return $this->name ;
    }
    
    /**
     * function setName
     */
    public function setName($name)
    {
        $this->name = $name ;
    }
}