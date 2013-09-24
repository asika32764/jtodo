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
    
    const WITHOUT_PREFIX = false;
    
    protected $prefix = 'Component';
    
    protected $container;
    
    protected $maps;
    
    protected $indexSymbol = '@';
    
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
     * getIndexSymbol description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  getIndexSymbolReturn
     *
     * @since  1.0
     */
    public function getIndexSymbol()
    {
        return $this->indexSymbol;
    }
    
    /**
     * function getController
     */
    public function getName($index)
    {
        // Finaly we use this '@SiteTodo/Categories/Category';
        
        // If $index is '@Component', we get lower case 'component' as key.
        if($name = $this->stripIndex($index))
        {
            $component = strtolower($name);
        }
        // Else if $index is the component namespace 'Subdir/Component', get key from maps.
        else
        {
            $maps = $this->getMaps();
            $maps = array_flip($maps);
            
            $name = $this->camelize($index);
            
            if(empty($maps[$name]))
            {
                throw new \InvalidArgumentException(sprintf('Component index: %s are not in maps', $index));
            }
            
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
    public function getNamespace($index, $prefix = true)
    {
        $maps = $this->getMaps();
        $key  = $this->getName($index);
        
        if($name = $this->stripIndex($index))
        {
            $name = strtolower($name);
            $name = $maps[$name];
        }
        else
        {
            $name = $index;
        }
        
        $namespace = $this->container->get('config')->get('component.' . $key);
        $namespace = $prefix ? trim($this->prefix, ' /\\') . '\\' . $name : $name;
        
        return $this->camelize($namespace);
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
    public function getPath($index, $absolute = true)
    {
        if(strpos($index, '.') !== false)
        {
            throw new \InvalidArgumentException(sprintf('Component index can not include dot (.), %s given.', $index));
        }
        
        $path = $this->getNamespace($index);
        
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $path);
        
        if($path == self::ABSOLUTE_PATH)
        {
            $path = JPATH_SOURCE . DIRECTORY_SEPARATOR . $path;
        }
        
        return $path;
    }
    
    /**
     * convertPathIndex description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  convertPathIndexReturn
     *
     * @since  1.0
     */
    public function convertPathIndex($path)
    {
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        
        $path = explode(DIRECTORY_SEPARATOR, $path);
        
        foreach($path as &$name)
        {
            if($this->isIndex($name))
            {
                $name = $this->getPath($name, false);
            }
        }
        
        return implode(DIRECTORY_SEPARATOR, $path);
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
     * isIndex description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  isIndexReturn
     *
     * @since  1.0
     */
    public function isIndex($name)
    {
        return ($name && is_string($name) && $name[0] == $this->indexSymbol) ? true : false;
    }
    
    /**
     * stripIndex description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  stripIndexReturn
     *
     * @since  1.0
     */
    public function stripIndex($index)
    {
        if(!$this->isIndex($index))
        {
            return false;
        }
        
        return str_replace($this->indexSymbol, '', $index);
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