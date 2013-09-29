<?php
/**
 * Part of the Joomla Standard Edition Component Package
 *
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace App\Joomla\Component;

use App\Joomla\Application\Application;
use App\Joomla\DI\ContainerAware;
use Joomla\Router\Router;
use Joomla\Input\Input;
use Joomla\DI\ServiceProviderInterface;
use Joomla\DI\Container;

abstract class Component extends ContainerAware implements ComponentInterface, ServiceProviderInterface
{
    /**
     * Component name.
     *
     * @var string
     * @since 1.0
     */
    protected $name = null ;
    
    /**
     * Default controller name.
     *
     * @var string
     * @since 1.0
     */
    protected $defaultController = null ;
    
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
     * Reflection object of self.
     *
     * @var \ReflectionClass
     * @since 1.0
     */
	protected $reflection;
    
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
	public function __construct(Application $application, Input $input, Container $container)
    {
        $this->application  = $application;
        $this->container    = $container;
        $this->input        = $input;
        
		$ref = $this->reflection = new \ReflectionClass($this);
        
        $name = str_replace('component', '', strtolower($ref->getShortName()));
        
        $this->name = $name;
        
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
        $container->share('component.' . $this->getName(), $this);
        
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
     * function setDefaultController
     */
    public function setDefaultController($name)
    {
        $this->defaultController = $name;
    }
    
    /**
     * function getDefaultController
     */
    public function getDefaultController()
    {
        // If default controller has not been setted, find a controller.
        if(!$this->defaultController)
        {
            $ctrls = new \FilesystemIterator($this->getPath() . '/Controller');
            
            foreach($ctrls as $ctrl)
            {
                $ctrlName = $ctrl->getBasename('.' . $ctrl->getExtension());
                
                if(strpos($ctrlName, 'Controller') !== false)
                {
                    $ctrlName = str_replace('Controller', '', $ctrlName);
                    $this->setDefaultController(ucfirst($ctrlName));
                    break;
                }
            }$this->defaultController;
            
            if(!$this->defaultController)
            {
                throw new \RuntimeException('No Controller found.');
            }
        }
        
        return $this->defaultController;
    }
    
    /**
     * function getController
     */
    public function getController($name, $input = null)
    {
        $container = $this->getContainer();
		
		$controller = $container->get('system.resolver.controller')->getInstance($name, $input);
        
        return $controller;
    }
    
    /**
     * function getPath
     */
    public function getPath()
    {
		if (null === $this->reflection) {
            $this->reflection = new \ReflectionObject($this);
        }

        return dirname($this->reflection->getFileName());
		
        //return JPATH_SOURCE . '/Component/' . ucfirst($this->getName());
    }
	
	/**
	 * function getNamespace
	 */
	public function getNamespace()
	{
		if (null === $this->reflection) {
            $this->reflection = new \ReflectionObject($this);
        }

        return $this->reflection->getNamespaceName();
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
    public function parseRoute($segments, Router $router)
    {
        $controller = array_shift($segments);
        $controller = $controller ?: $this->getDefaultController();
        
        $maps = json_decode(file_get_contents($this->getPath() . '/Config/routing.json'));
        
        if (!$maps)
        {
            throw new \RuntimeException('Invalid router file.', 500);
        }
        
        foreach((array)$maps as $map)
        {
            $router->addMap($map->pattern, $map->controller);
        }
        
        // Get default routes if not any route setting matchs.
        $this->parseDefaultRoute($controller, $router);
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
    public function buildRoute($query, Router $router)
    {
        
    }
    
    /**
     * function getRouterConfig
     */
    public function getRouting($controller, Router $router)
    {
        $file       = __DIR__.'/Routing/routing.json';
        $maps    = json_decode(file_get_contents($file));
        
        foreach($maps as $map)
        {
            $replace = array(
                '{:component}'  => ucfirst($this->getName()),
                '{:controller}' =>  ucfirst($controller)
            );
            
            $controller = strtr($map->controller, $replace);
            
            $router->addMap($map->pattern, $controller);
        }
        
        
        //show($router);die;
    }
    
    /**
     * function getDefaultRouting
     */
    public function parseDefaultRoute($defaultController, Router $router)
    {
        $file       = __DIR__.'/Routing/routing.json';
        $maps    = json_decode(file_get_contents($file));
        
        foreach((array)$maps as $map)
        {
            $replace = array(
                '{:component}'  => ucfirst($this->getName()),
                '{:controller}' =>  ucfirst($defaultController)
            );
            
            $controller = strtr($map->controller, $replace);
            
            $router->addMap($map->pattern, $controller);
        }
    }
}