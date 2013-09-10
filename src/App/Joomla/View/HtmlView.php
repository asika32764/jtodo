<?php
/**
 * Part of the Joomla Tracker View Package
 *
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace App\Joomla\View;

use Joomla\Factory;
use Joomla\Language\Text;
use Joomla\Model\ModelInterface;
use Joomla\View\Renderer\RendererInterface;

use Joomla\View\AbstractView;
use App\Joomla\Application\TrackerApplication;
use App\Joomla\View\Renderer\AppExtension;

/**
 * Abstract HTML view class for the Tracker application
 *
 * @since  1.0
 */
abstract class HtmlView extends AbstractView
{
	/**
	 * The view layout.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $layout = 'index';

	/**
	 * The view template engine.
	 *
	 * @var    RendererInterface
	 * @since  1.0
	 */
	protected $renderer = null;

	public $templatePath;
	
	protected $namespace = null;
	
	/**
	 * Method to instantiate the view.
	 *
	 * @param   ModelInterface  $model           The model object.
	 * @param   string|array    $templatesPaths  The templates paths.
	 *
	 * @throws  \RuntimeException
	 * @since   1.0
	 */
	public function __construct(ModelInterface $model = null)
	{
		parent::__construct($model);

		/* @type TrackerApplication $app */
		$app = Factory::$application;

		// Set Template path
		$ref = new \ReflectionClass($this);
		
		$name = $ref->getName();
		
		$name = explode('View', $name);
		$this->namespace = $name[0];
		$viewName = explode('\\', $name[1]);
		$viewName = $viewName[1];
		
		$this->templatePath = JPATH_SOURCE . '/' . str_replace('\\', '/', $this->namespace) . 'Template/' ;
		$this->setLayout('default');
		
		$templatesPaths = array(JPATH_TEMPLATES, $this->templatePath, );
		
		
		// Get Renderer
		$renderer = $app->get('renderer.type');

		$className = 'App\\Joomla\\View\\Renderer\\' . ucfirst($renderer);

		// Check if the specified renderer exists in the application
		if (false == class_exists($className))
		{
			$className = 'Joomla\\View\\Renderer\\' . ucfirst($renderer);

			// Check if the specified renderer exists in the Framework
			if (false == class_exists($className))
			{
				throw new \RuntimeException(sprintf('Invalid renderer: %s', $renderer));
			}
		}

		$config = array();
		$config['templates_base_dir'] = $this->templatePath . $viewName;
		$config['environment']['debug'] = JDEBUG ? true : false;


		// Load the renderer.
		$this->renderer = new $className($config);

		// Register tracker's extension.
		$this->renderer->addExtension(new AppExtension);

		// Register additional paths.
		if (!empty($templatesPaths))
		{
			$this->renderer->setTemplatesPaths($templatesPaths, true);
		}

		//$gitHubHelper = new GitHubLoginHelper($app->get('github.client_id'), $app->get('github.client_secret'));

		//$this->renderer
			//->set('user', $app->getUser());

		// Retrieve and clear the message queue
		$this->renderer->set('flashBag', $app->getMessageQueue());
		$app->clearMessageQueue();
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

	/**
	 * Method to escape output.
	 *
	 * @param   string  $output  The output to escape.
	 *
	 * @return  string  The escaped output.
	 *
	 * @see     ViewInterface::escape()
	 * @since   1.0
	 */
	public function escape($output)
	{
		// Escape the output.
		return htmlspecialchars($output, ENT_COMPAT, 'UTF-8');
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
		return $this->layout;
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
	 * Method to get template base path.
	 * 
	 * @param   string
	 *
	 * @return  string
	 */
	public function getTemplatePath()
	{
		return $this->templatePath;
	}
	
	/**
	 * function setTemplatePath
	 */
	public function setTemplatePath($path)
	{
		$this->templatePath = $path;
	}
}
