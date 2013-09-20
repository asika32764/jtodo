<?php
/**
 * Part of the Joomla Tracker View Package
 *
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace App\Joomla\View;

use Joomla\View\AbstractView;
use Joomla\Model\ModelInterface;
use Joomla\Data\DataObject;

use App\Joomla\View\ViewInterface;
use App\Joomla\View\Layout\Layout;
use App\Joomla\View\Renderer\RendererInterface;

/**
 * Default view class for the Tracker application
 *
 * @since  1.0
 */
class View extends AbstractView implements ViewInterface
{
    protected $renderer;
	
	protected $layoutHandler;
	
	protected $layout;
    
    protected $reflection;
    
    protected $name;
    
    protected $path;
    
    protected $namespace;
	
	protected $data;
    
    /**
     * function getModel
     */
    public function getModel()
    {
        return $this->model;
    }
	
	/**
     * function getModel
     */
    public function setModel($model)
    {
        $this->model = $model;
		
		return $this;
    }
	
	/**
	 * set description
	 *
	 * @param  string
	 * @param  string
	 * @param  string
	 *
	 * @return  string  setReturn
	 *
	 * @since  1.0
	 */
	public function set($key, $value)
	{
		$this->getData()->$key = $value;
		
		return $this;
	}
	
	/**
	 * get description
	 *
	 * @param  string
	 * @param  string
	 * @param  string
	 *
	 * @return  string  getReturn
	 *
	 * @since  1.0
	 */
	public function get($key, $default)
	{
		return $this->getData()->$key ?: $default;
	}
	
	/**
	 * getData description
	 *
	 * @param  string
	 * @param  string
	 * @param  string
	 *
	 * @return  string  getDataReturn
	 *
	 * @since  1.0
	 */
	public function getData()
	{
		$this->data = $this->data ?: new DataObject();
		return $this->data;
	}
    
    /**
     * function setRenderer
     */
    public function setRenderer(RendererInterface $renderer, $type = null)
    {
		if(!$type)
		{
			$ref = new \ReflectionClass($renderer);
			$name = $ref->getShortName();
			$type = substr($name, 0, -8);
		}
		
		strtolower($type);
		
        $this->renderer[$type] = $renderer;
		
		return $this;
    }
    
    /**
	 * Method to get the renderer object.
	 *
	 * @return  RendererInterface  The renderer object.
	 *
	 * @since   1.0
	 */
	public function getRenderer($type = null)
	{
		strtolower($type);
		
		if($type)
		{
			return $this->renderer[$type];
		}
		
		$key = key($this->renderer);
		
		return $this->renderer[$key];
	}
	
	/**
	 * Method to get the view layout.
	 *
	 * @return  string  The layout name.
	 *
	 * @since   1.0
	 */
	public function getLayout()
	{
		return $this->layout ?: 'default';
	}

	/**
	 * Method to set the view layout.
	 *
	 * @param   string  $layout  The layout name.
	 *
	 * @return  $this  Method supports chaining
	 *
	 * @since   1.0
	 */
	public function setLayout($layout)
	{
		$this->layout = $layout;

		return $this;
	}
	
	/**
	 * Method to get the view layout.
	 *
	 * @return  string  The layout name.
	 *
	 * @since   1.0
	 */
	public function getLayoutHandler($layoutName = null)
	{
		$layoutName = $layoutName ?: $this->getLayout();
		
		if(!empty($this->layoutHandler[$layoutName]))
		{
			return $this->layoutHandler[$layoutName];
		}
		
		return $this->layoutHandler[$layoutName] = new Layout($layoutName);
	}

	/**
	 * Method to set the view layout.
	 *
	 * @param   string  $layout  The layout name.
	 *
	 * @return  $this  Method supports chaining
	 *
	 * @since   1.0
	 */
	public function setLayoutHandler(LayoutInterface $handler, $layoutName = null)
	{
		$this->layoutHandler[$layoutName ?: $handler->getName()] = $handler;

		return $this;
	}
    
    /**
	 * function getName
	 */
	public function getName()
	{
		if($this->name)
        {
            return $this->name;
        }
        
        $name = $this->getReflection()->getNamespaceName();
        $name = explode('\\View\\', $name);
        $name = array_pop($name);
        
        return $this->name = trim($name, '\\');
	}
	
	/**
	 * function getPath
	 */
	public function getPath()
	{
		if($this->path)
        {
            return $this->path;
        }
        
        $path = $this->getReflection()->getFileName();
        
        return $this->path = dirname($path);
	}
    
    /**
     * function getReflection
     */
    public function getReflection()
    {
        if($this->reflection)
        {
           return $this->reflection; 
        }
        
        return $this->reflection = new \ReflectionClass($this);
    }
    
    /**
     * function getNamespace
     */
    public function getNamespace()
    {
        if($this->namespace)
        {
            return $this->namespace;
        }
        
        $namespace = $this->getReflection()->getNamespaceName();
        
        return $this->namespace = $namespace;
    }
    
    /**
	 * Method to render the view.
	 *
	 * @return  string  The rendered view.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function render()
	{
		return $this->layoutHandler->render($this->data, $this->layout);
	}
    
    /**
	 * Magic toString method that is a proxy for the render method.
	 *
	 * @return  string
	 *
	 * @since   1.0
	 */
	public function __toString()
	{
		return $this->render();
	}
}
