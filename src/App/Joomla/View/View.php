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

use Joomla\Factory;
use Joomla\Language\Text;

use App\Joomla\View\Renderer\RendererInterface;
use App\Joomla\View\Renderer\AppExtension;
use App\Joomla\View\ViewInterface;

//use JTracker\Model\TrackerDefaultModel;

/**
 * Default view class for the Tracker application
 *
 * @since  1.0
 */
class View extends AbstractView implements ViewInterface
{
    protected $renderer;
    
    protected $reflection;
    
    protected $name;
    
    protected $path;
    
    protected $namespace;
    
    /**
     * function getModel
     */
    public function getModel()
    {
        return $this->model;
    }
    
    /**
     * function setRenderer
     */
    public function setRenderer(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }
    
    /**
	 * Method to get the renderer object.
	 *
	 * @return  RendererInterface  The renderer object.
	 *
	 * @since   1.0
	 */
	public function getRenderer()
	{
		return $this->renderer;
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
		return $this->renderer->render($this->layout);
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
