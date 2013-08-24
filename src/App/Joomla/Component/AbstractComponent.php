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
	 * @param   Application  $application  The application
	 * @param   Container    $container    DI Container
	 * @param   string       $name         A component name alias in container, if is null,
	 *                                     will get from class name.
	 *
	 * @since   1.0
	 */
	public function __construct(Application $application, Container $container, $name = null)
    {
        $this->application = $application;
        
		$ref = new \ReflectionClass($this);
        
        if(!$name)
        {
            $name = str_replace('component', '', strtolower($ref->getShortName()));
        }
        
        $this->setName($name);
        
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
        $container->set('component.' . $this->getName(), $this);
        
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