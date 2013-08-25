<?php
/**
 * Part of the Joomla Standard Edition Component Package
 *
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace App\Joomla\Component;

use App\Joomla\Application\Application;
use Joomla\Router\Router;
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
                $ctrlName = $ctrl->getFilename();
                
                if($strpos('Controller', $ctrlName) !== false)
                {
                    $ctrlName = str_replace('Controller', '', $ctrlName);
                    $this->setDefaultController(ucfirst($ctrlName));
                    break;
                }
            }
            
            if(!$this->defaultController)
            {
                throw new \RuntimeException('No Controller found.');
            }
        }
        
        return $this->defaultController;
    }
    
    /**
     * function getPath
     */
    public function getPath()
    {
        return JPATH_SOURCE . '/Components/' . ucfirst($this->getName());
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
    public function getRouting($defaultController, Router $router)
    {
        $file       = __DIR__.'/Routing/routing.json';
        $maps    = json_decode(file_get_contents($file));
        
        foreach($maps as $map)
        {
            $replace = array(
                '{:component}'  => ucfirst($this->getName()),
                '{:controller}' =>  ucfirst($defaultController)
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