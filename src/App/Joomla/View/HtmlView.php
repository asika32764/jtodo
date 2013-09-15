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

use App\Joomla\View\Renderer\RendererInterface;
use App\Joomla\Application\TrackerApplication;
use App\Joomla\View\View;
use App\Joomla\View\ViewInterface;
use App\Joomla\View\Renderer\AppExtension;

/**
 * Abstract HTML view class for the Tracker application
 *
 * @since  1.0
 */
abstract class HtmlView extends View implements ViewInterface
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

		/* @type TrackerApplication $app */
		$app = Factory::$application;
		
		// Set Template paths
		$templatePath = $this->getPath() . '/../../Template' ;
		$templatePath = realpath($templatePath);
		
		$basePath = $templatePath . '/' . $this->getName();

		// Register additional paths.
		$renderer->setTemplatesPaths(array($basePath, $templatePath, JPATH_TEMPLATES), true);

		// Retrieve and clear the message queue
		$renderer->set('flashBag', $app->getMessageQueue());
		$app->clearMessageQueue();
		
		$this->renderer = $renderer;
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
