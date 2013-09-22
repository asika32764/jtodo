<?php
/**
 * Part of the Joomla Edition Controller Package
 *
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace App\Joomla\Component;

use App\Joomla\Factory;
use App\Joomla\DI\ContainerAware;

/**
 * Abstract Controller class for the Tracker Application
 *
 * @since  1.0
 */
class ComponentResolver extends ContainerAware 
{
    const ABSOLUTE_PATH = true;
    
    protected $prefix = 'Component';
    
    protected $container;
    
    protected $maps;
    
    /**
     * loadComponent description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  loadComponentReturn
     *
     * @since  1.0
     */
    public function loadComponent($key, $component, $application = null, $input = null)
    {
        $name = $this->splitName($component);
        
        $class = $this->prefix . '\\' . $name['namespace'] . '\\' . $name['name'] ;
        
        // Check for the requested controller.
        if (!class_exists($class) || !is_subclass_of($class, 'App\\Joomla\\Component\\ComponentInterface'))
        {
            throw new \RuntimeException($class.' not found');
        }
        
        $application = $application ?: $this->container->get('application');
        $input       = $input ?: $application->input;
        
        $this->container->share('component.' . strtolower($key), function($container) use ($class, $input, $application) {
            return new $class($application, $input, $container);
        });
        
        return $this;
    }
    
    /**
     * setPrefix description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  setPrefixReturn
     *
     * @since  1.0
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $this->camelize($prefix);
    }
    
    /**
     * getPrefix description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  getPrefixReturn
     *
     * @since  1.0
     */
    public function getPrefix()
    {
        return $this->prefix;
    }
    
    /**
     * function getController
     */
    public function getName($name)
    {
        // Finaly we use this '@SiteTodo/Categories/Category';
        
        if(!$name)
        {
            throw new \InvalidArgumentException('Component name is empty.');
        }
        
        if($name[0] == '@')
        {
            $component = strtolower(str_replace('@', '', $name));
        }
        else
        {
            $maps = $this->getMaps();
            $maps = array_flip($maps);
            
            $name = $this->camelize($name);   
            
            $component = $maps[$name];
        }
        
        return $component;
    }
    
    /**
     * getComponentPath description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  getComponentPathReturn
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
     * getComponentPath description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  getComponentPathReturn
     *
     * @since  1.0
     */
    public function getNamespace($name)
    {
        $component = $this->getName($name);
        
        $namespace = $this->container->get('config')->get('component.' . $name);
        
        $namespace = trim($this->prefix, ' /\\') . '\\' . $namespace;
        
        return $this->camelize($name);
    }
    
    /**
     * getComponent description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  getComponentReturn
     *
     * @since  1.0
     */
    public function getInstance($name)
    {
        $component = $this->getName($name);
        
        try
        {
            $component = $container->get('component.' . $component);
        }
        catch(\Exception $e)
        {
            throw new \RuntimeException(sprintf('Component %s not found.', '@' . $component), null, $e);
        }
        
        return $component;
    }
    
    /**
     * getComponentMap description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  getComponentMapReturn
     *
     * @since  1.0
     */
    public function getMaps()
    {
        if($this->maps)
        {
            return $this->maps;
        }
        
        $maps = $this->container->get('config')->get('component');
        
        return $this->maps = array_change_key_case ((array) $maps, CASE_LOWER);
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
        
        $return = array();
        
        if(count($name) <= 1)
        {
            $return['name']           = $name[0] . 'Component';
            $return['namespace_name'] = $name[0] . '\\' . $return['name'];
            $return['namespace']      = $name[0];
        }
        else
        {
            $com_name = array_pop($name);
            $return['name']           = $com_name . 'Component';
            $return['namespace_name'] = implode('\\', $name) . '\\' . $com_name . '\\' . $return['name'];
            $return['namespace']      = implode('\\', $name) . '\\' . $com_name;
        }
        
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