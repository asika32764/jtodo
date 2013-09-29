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
use Joomla\DI\Container;

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
	
	protected $layout;
    
    protected $reflection;
    
    protected $name;
    
    protected $path;
    
    protected $namespace;
	
	protected $data;
	
	protected $container;
    
	/**
	 * Method to instantiate the view.
	 *
	 * @param   ModelInterface  $model           The model object.
	 * @param   string|array    $templatesPaths  The templates paths.
	 *
	 * @throws  \RuntimeException
	 * @since   1.0
	 */
	public function __construct(ModelInterface $model = null, RendererInterface $renderer = null)
	{
		parent::__construct($model, $renderer);
		
		$app = \Joomla\Factory::$application;
		
		$templatePath = $this->getPath() . '/../../Template' ;
		$templatePath = realpath($templatePath);
		
		$basePath = $templatePath . '/' . $this->getName();
		
		// Set Template paths
		$this->templatePaths = array(
			'Self'      => $basePath,
			'Component' => $templatePath,
			'Global'    => JPATH_TEMPLATES
		);
		
		$renderer->setPaths($this->templatePaths);
		
		// Retrieve and clear the message queue
		//$this->set('flashBag', $app->getMessageQueue());
		//$app->clearMessageQueue();
		
		$this->setRenderer($renderer);
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
    public function setContainer(Container $container = null)
    {
        $this->container = $container;
    }
    
    /**
	 * Set the DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @since   1.0
	 */
    public function getContainer()
    {
        return $this->container;
    }
	
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
	public function set($key, $value = null)
	{
		if(is_array($key))
        {
            foreach($key as $k => $v)
            {
                $this->set($k, $v);
            }
        }
        else
		{
			$this->getData()->$key = $value;
		}
		
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
	public function get($key)
	{
		return $this->getData()->$key;
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
		if($this->data)
		{
			return $this->data;
		}
		
		return $this->data = new DataObject();
	}
    
    /**
     * function setRenderer
     */
    public function setRenderer(RendererInterface $renderer, $type = null)
    {
        $this->renderer = $renderer;
		
		return $this;
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
        if($this->reflection instanceof \ReflectionClass)
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
		$renderer = $this->getRenderer();
		
		$this->set('router', $this->container->get('router'));
		
		return $renderer->render($this->getLayout(), (array) $this->getData()->dump());
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
