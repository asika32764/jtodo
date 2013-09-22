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
    
    /**
     * function getController
     */
    public static function getName($name)
    {
        $container = Factory::getContainer();
        
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
            $component = explode('/', $name);
            $component = $component[0];
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