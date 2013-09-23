<?php
/**
 * Part of the Joomla Edition Controller Package
 *
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace App\Joomla\Controller;

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
        
        $namespace = $name['component_namespace'] . '\\Controller\\' . $name['controller'];
        
        $namespace = $name['action'] ? $namespace . '\\' . $name['action'] . 'Controller' : $namespace;
        
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
    public function getInstance($name, $data = null)
    {
        $container = $this->getContainer();
        
        // @SubdirTodo:Category:Save
        $name = $this->camelize($name);
        $name = explode('\\', $name);
        
        if(count($name) == 2)
        {
            list($component, $controller) = $name;
            $action = null;
        }
        elseif(count($name) == 3)
        {
            list($component, $controller, $action) = $name;
        }
        else
        {
            throw new \InvalidArgumentException(sprintf('Controller %s not found.', implode(':', $name)));
        }
        
        $component = strtolower(str_replace('@', '', $component));
        
        try
        {
            $component = $container->get('component.' . $component);
        }
        catch(\Exception $e)
        {
            throw new \RuntimeException(sprintf('Component %s not found.', $component), null, $e);
        }
        
        $controller = $component->getController($controller, $action, $data);
        
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