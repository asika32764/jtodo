<?php
/**
 * Part of the Joomla Tracker View Package
 *
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace App\Joomla\View;

use Joomla\Model\ModelInterface;

use App\Joomla\View\View;
use App\Joomla\View\Renderer\RendererInterface;

/**
 * Abstract HTML view class for the Tracker application
 *
 * @since  1.0
 */
abstract class HtmlView extends View
{
	public $templatePaths = array();
	
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
		
		// Retrieve and clear the message queue
		//$this->set('flashBag', $app->getMessageQueue());
		//$app->clearMessageQueue();
		
		$this->setRenderer($renderer);
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
	 * Method to get template base path.
	 * 
	 * @param   string
	 *
	 * @return  string
	 */
	public function getTemplatePaths()
	{
		return $this->templatePaths;
	}
	
	/**
	 * function setTemplatePath
	 */
	public function setTemplatePaths($path)
	{
		$this->templatePaths = $path;
	}
	
	/**
	 * render description
	 *
	 * @param  string
	 * @param  string
	 * @param  string
	 *
	 * @return  string  renderReturn
	 *
	 * @since  1.0
	 */
	public function render()
	{
		// Layout
		$layout = $this->getLayoutHandler($this->getLayout());
		
		// TODO: move all template setting to layout object
		$layout->setPaths($this->getTemplatePaths());
		
		$layout->setRenderer($this->getRenderer());
		
		return $layout->render($this->getData());
	}
}
