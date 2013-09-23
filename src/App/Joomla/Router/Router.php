<?php
/**
 * Part of the Joomla Tracker Router Package
 *
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace App\Joomla\Router;

use Joomla\Application\AbstractApplication;
use Joomla\Controller\ControllerInterface;
use Joomla\Input\Input;
use Joomla\Router\RestRouter as JoomlaRouter;
use Joomla\Factory;
use Joomla\DI\Container;

use Joomla\DI\ContainerAwareInterface;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Filesystem\Path;

use App\Joomla\Router\Exception\RoutingException;

/**
 * Joomla! Tracker Router
 *
 * @since  1.0
 */
class Router extends JoomlaRouter implements ContainerAwareInterface, ServiceProviderInterface
{
	/**
	 * Application object to inject into controllers
	 *
	 * @var    AbstractApplication
	 * @since  1.0
	 */
	protected $app;
	
	/**
	 * @var     boolean  A boolean allowing to pass _method as parameter in POST requests
	 *
	 * @since  1.0
	 */
	protected $methodInPostRequest = true;

	/**
	 * @var    array  An array of HTTP Method => controller suffix pairs for routing the request.
	 * @since  1.0
	 */
	protected $suffixMap = array(
		'GET' => 'Index',
		'POST' => 'Add',
		'PUT' => 'Save',
		'PATCH' => 'Save',
		'DELETE' => 'Delete',
		'HEAD' => 'Head',
		'OPTIONS' => 'Options'
	);

	/**
	 * Constructor.
	 *
	 * @param   Input                $input  An optional input object from which to derive the route.  If none
	 *                                       is given than the input from the application object will be used.
	 * @param   AbstractApplication  $app    An optional application object to inject to controllers
	 *
	 * @since   1.0
	 */
	public function __construct(Input $input = null, Container $container = null)
	{
		parent::__construct($input);
		
		$this->container = $container;
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
        $container->share('router', $this);
        
        return $this;
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
    public function getContainer()
    {
        if($this->container instanceof Container) {
            return $this->container ;
        }
        
        $this->setContainer(new Container);
        
        return $this->container ;
    }
    
    /**
     * Set the DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @since   1.0
     */
    public function setContainer(Container $container)
    {
        $this->container = $container ;
    }
	
	/**
	 * defineRouting description
	 *
	 * @param  string
	 * @param  string
	 * @param  string
	 *
	 * @return  string  defineRoutingReturn
	 *
	 * @since  1.0
	 */
	public function loadRouter()
	{
		$config  = $this->container->get('config');
		$routers = (array) $config->get('routing');
		
		$resolver = $this->container->get('system.resolver.component');
		
		$component = array();
		$routing   = new \Stdclass;
		
		foreach( $routers as $key => &$router )
		{
			// If controller setted, use controller resource
			if(isset($router->controller))
			{
				$routing->$key = $router;
				continue;
			}
			
			// If include setted, we parse component routing config
			if(isset($router->include))
			{
				$include = $router->include;
				
				// Get config by component index
				if($resolver->isIndex($include))
				{
					$include = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $include);
					
					$splited = explode(DIRECTORY_SEPARATOR, $include);
					
					if(count($splited) == 1)
					{
						// Using index and get default routing file
						// Example: @Component
						$include = $resolver->getPath($include) . '/Config/routing.json';
					}
					elseif(count($splited) > 1)
					{
						// Using index to load file by absolute path
						// Example: @Component/Config/routing.json
						$include = $resolver->convertPathIndex($include);
					}
				}
				// Get config by component namespace
				else
				{
					try
					{
						// Relative path as namespace
						// Example: Subdir/Component
						$include = $resolver->getPath($include) . '/Config/routing.json';
					}
					catch(\InvalidArgumentException $e)
					{
						// If get path failure, meaning it is an absolute path
						// Example: Subdir/Component/Config/routing.json
						$include = JPATH_SOURCE . '/' . $resolver->getPrefix() . '/' . $include;
					}
				}
				
				$include = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $include);
				
				// Load include config
				if(is_file($include))
				{
					$comRoutes = new \App\Joomla\Registry\Registry;
					
					$comRoutes->loadFile($include);
					
					$comRoutes = $comRoutes->toObject();
					
					foreach((array) $comRoutes as $subkey => $route)
					{
						$route->pattern = rtrim($router->pattern, '/') . '/' . trim($route->pattern, '/');
						$newkey = $key . '/' . $subkey;
						$routing->$newkey = $route;
					}
					
					continue;
				}
				else
				{
					throw new \InvalidArgumentException(sprintf('Routing config %s not found.', $include));
				}
			}
			
			// controller & include are not exists, throw error
			throw new \InvalidArgumentException(sprintf('Router \'%s\' needs \'include\' or \'controller\' resource.', $key));
		}
		
		// Now, we add these routers to Map
		foreach((array) $routing as $route)
		{
			if(!$route->pattern || $route->pattern == '/')
			{
				$route->pattern = '*';
			}
			
			$this->addMap($route->pattern, $route->controller);
		}
		
		// Reset routing to config
		$config->set('routing', $routing);
		show($this->maps);
		return $this;
	}

	/**
	 * Find and execute the appropriate controller based on a given route.
	 *
	 * @param   string  $route  The route string for which to find and execute a controller.
	 *
	 * @return  ControllerInterface
	 *
	 * @since   1.0
	 * @throws  RoutingException
	 */
	public function getController($route)
	{
		try
		{
			return parent::getController($route);
		}
		catch (\InvalidArgumentException $e)
		{
			// 404
			throw new RoutingException($e->getMessage());
		}
		catch (\RuntimeException $e)
		{
			// 404
			throw new RoutingException($e->getMessage());
		}
	}
	
	/**
	 * Parse the given route and return the name of a controller mapped to the given route.
	 *
	 * @param   string  $route  The route string for which to find and execute a controller.
	 *
	 * @return  string  The controller name for the given route excluding prefix.
	 *
	 * @since   1.0
	 * @throws  \InvalidArgumentException
	 */
	protected function parseRoute($route)
	{
		$controller = false;

		// Trim the query string off.
		$route = preg_replace('/([^?]*).*/u', '\1', $route);

		// Sanitize and explode the route.
		$route = trim(parse_url($route, PHP_URL_PATH), ' /');

		// If the route is empty then simply return the default route.  No parsing necessary.
		if ($route == '')
		{
			return $this->default;
		}
		

		// Iterate through all of the known route maps looking for a match.
		foreach ($this->maps as $rule)
		{
			if (preg_match($rule['regex'], $route, $matches))
			{
				// If we have gotten this far then we have a positive match.
				$controller = $rule['controller'];

				// Time to set the input variables.
				// We are only going to set them if they don't already exist to avoid overwriting things.
				foreach ($rule['vars'] as $i => $var)
				{
					$this->input->def($var, $matches[$i + 1]);

					// Don't forget to do an explicit set on the GET superglobal.
					$this->input->get->def($var, $matches[$i + 1]);
				}

				$this->input->def('_rawRoute', $route);

				break;
			}
		}

		// We were unable to find a route match for the request.  Panic.
		if (!$controller)
		{
			throw new \InvalidArgumentException(sprintf('Unable to handle request for route `%s`.', $route), 404);
		}

		return $controller;
	}
	
	/**
	 * Get a JController object for a given name.
	 *
	 * @param   string  $name  The controller name (excluding prefix) for which to fetch and instance.
	 *
	 * @return  ControllerInterface
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	protected function fetchController($name)
	{
		// Derive the controller class name.
		echo $class = $this->controllerPrefix . ucfirst($name);
		die;
		// If the controller class does not exist panic.
		if (!class_exists($class) || !is_subclass_of($class, 'Joomla\\Controller\\ControllerInterface'))
		{
			throw new \RuntimeException(sprintf('Unable to locate controller `%s`.', $class), 404);
		}

		// Instantiate the controller.
		$controller = new $class($this->input, $this->container->get('application'));

		return $controller;
	}
}
