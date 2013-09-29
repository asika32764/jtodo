<?php
/**
 * Part of the Joomla Edition Controller Package
 *
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace App\Joomla\Controller;

use Joomla\Input\Input;

use App\Joomla\Factory;
use App\Joomla\DI\ContainerAware;


/**
 * Abstract Controller class for the Tracker Application
 *
 * @since  1.0
 */
class ControllerResolver extends ContainerAware 
{
    const ABSOLUTE_PATH = true;
    
    /**
     * getName description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  getNameReturn
     *
     * @since  1.0
     */
    public function getName($name)
    {
        $name = $this->camelize($name);
        
        $name = explode('\\', $name);
        
        if(count($name) < 2 || $name[0][0] != '@')
        {
            throw new \InvalidArgumentException(sprintf('Invaild Controller path: %s .', implode($name)));
        }
        
        return $name[1];
    }
    
    /**
     * getNamespace description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  getNamespaceReturn
     *
     * @since  1.0
     */
    public function getNamespace($name)
    {
        $name = $this->splitName($name);
        
        $namespace = $name['component_namespace'] . '\\Controller\\' . $name['controller'] . '\\' . $name['controller'] . 'Controller';
        
        $namespace = $name['action'] ? $namespace . $name['action'] : $namespace;
        
        return $namespace;
    }
    
    /**
     * getPath description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  getPathReturn
     *
     * @since  1.0
     */
    public function getPath($name, $absolute = true)
    {
        $path = $this->getNamespace($name);
        
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $path);
        
        if($path == self::ABSOLUTE_PATH)
        {
            $path = JPATH_SOURCE . DIRECTORY_SEPARATOR . $path;
        }
        
        return $path;
    }
    
    /**
     * function getController
     */
    public function getInstance($name, $input = null)
    {
        $container   = $this->getContainer();
        $comResolver = $container->get('system.resolver.component');
        
        $splited = $this->splitName($name);
        
        if(!($input instanceof Input))
		{
			$input = new Input((array) $input);
		}
        
        try
        {
            $controllername = 'component.' . $splited['component'] . '.' . strtolower($splited['controller']);
            $controllername = !$splited['action'] ? $controllername : $controllername . '.' . strtolower($splited['action']);
            $controller     = $container->get($controllername);
        }
        catch(\Exception $e)
        {
            $classname   = $splited['component_namespace'] . '\\Controller\\' . $splited['controller'] .
                            '\\' . $splited['controller'] . 'Controller' . $splited['action'];
            
            $application = $container->get('application');
            
            // Support lazyloading
            $container->protect($controllername, function($container) use ($classname, $application, $input)
            {
                $controller = new $classname($input, $application);
                
                $controller->setContainer($container);
                
                return $controller;
            });
            
            $controller = $container->get($controllername);
        }
        
        return $controller;
    }
    
    /**
     * splitName description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  splitNameReturn
     *
     * @since  1.0
     */
    public function splitName($name)
    {
        $name = $this->camelize($name);
        
        $name = explode('\\', $name);
        
        if(!count($name))
        {
            throw new \InvalidArgumentException('Need Component name.');
        }
        elseif($name[0][0] != '@')
        {
            throw new \InvalidArgumentException(sprintf('Need Component name start with "@", %s given.', $name));
        }
        
        // Define name path
        $return = array();
        
        $return['component'] = trim(array_shift($name));
        
        if(count($name))
        {
            $return['controller'] = array_shift($name);
        }
        
        if(count($name))
        {
            $return['action'] = array_shift($name);
        }
        
        // Get component name
        $componentResolver = $this->container->get('system.resolver.component');
        
        $return['component_namespace'] = $componentResolver->getNamespace($return['component']);
        $return['component'] = $componentResolver->getName($return['component']);
        
        return $return;
    }
    
    /**
     * camelize description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  camelizeReturn
     *
     * @since  1.0
     */
    public function camelize($name)
    {
        $name = str_replace(array('\\', '/'), ' ', $name);
        
        $name = ucwords($name);
        
        return $name = str_replace(' ', '\\', $name);
    }
}