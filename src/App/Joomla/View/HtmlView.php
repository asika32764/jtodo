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

use Joomla\View\Layout\LayoutInterface;
use Joomla\Filesystem\Path\PathLocator;
use Joomla\Filesystem\Path\PathCollection;

use App\Joomla\Application\TrackerApplication;
use App\Joomla\View\View;
use App\Joomla\View\ViewInterface;
use App\Joomla\View\Renderer\RendererInterface;
use App\Joomla\View\Renderer\AppExtension;
use App\Joomla\View\Layout\Layout;

/**
 * Abstract HTML view class for the Tracker application
 *
 * @since  1.0
 */
abstract class HtmlView extends View implements ViewInterface
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
	public function __construct(ModelInterface $model = null, RendererInterface $renderer = null, LayoutInterface $layout = null)
	{
		parent::__construct($model, $renderer, $layout);
		
		$app = Factory::$application;
		
		$templatePath = $this->getPath() . '/../../Template' ;
		$templatePath = realpath($templatePath);
		
		$basePath = $templatePath . '/' . $this->getName();
		
		// Set Template paths
		$this->templatePaths = new PathCollection(array(
			'Self'      => new PathLocator($basePath),
			'Component' => new PathLocator($templatePath),
			'Global'    => new PathLocator(JPATH_TEMPLATES)
		));
		
		if(!$layout)
		{
			$layout = new Layout($this->templatePaths);
		}
		
		$layout->setRenderer($renderer);
		$this->layoutHandler = $layout;

		// Retrieve and clear the message queue
		//$this->set('flashBag', $app->getMessageQueue());
		//$app->clearMessageQueue();
		
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
	public function setTemplatePaths($path)
	{
		$this->templatePaths = $path;
	}
	
	
}
