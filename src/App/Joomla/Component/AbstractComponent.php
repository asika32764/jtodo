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
        $this->application  = $application;
        $this->container    = $container;
        
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
    
    /**
     * Parse uri segments as route.
     *
     * @param   string  $segments   URI segments.
     *
     * @return  array   Query vars.
     *
     * @since   1.0
     */
    public function parseRoute($segments)
    {
        $controller = array_shift($segments);
        //$controller = '\\' . ucfirst($this->getName()) . '\\Controller\\' . ucfirst($controller) . 'Controller' ;
        $this->getRouterMapping($controller);
        
    }
    
    /**
     * Build query to uri.
     *
     * @param   string  $query   URL query.
     *
     * @return  array   Uri segments.
     *
     * @since   1.0
     */
    public function buildRoute($query)
    {
        
    }
    
    /**
     * function getRouterConfig
     */
    public function getRouterMapping($defaultController)
    {
        $file       = __DIR__.'/Routing/routing.json';
        $mapping    = json_decode(file_get_contents($file));
        
        $router = $this->container->get('system.router');
        
        foreach($mapping->_default as $routing)
        {
            $replace = array(
                '{:component}'  => ucfirst($this->getName()),
                '{:controller}' =>  ucfirst($defaultController)
            );
            
            $controller = strtr($routing->controller, $replace);
            
            $router->addMap($routing->pattern, $controller);
        }
        
        
        //show($router);die;
    }
}