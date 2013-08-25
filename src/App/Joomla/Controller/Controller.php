<?php
/**
 * Part of the Joomla Tracker Controller Package
 *
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace App\Joomla\Controller;

use Joomla\Application\AbstractApplication;
use Joomla\Controller\AbstractController;
use Joomla\Input\Input;
use Joomla\Log\Log;
//use JTracker\Application\TrackerApplication;
//use JTracker\View\AbstractTrackerHtmlView;

/**
 * Abstract Controller class for the Tracker Application
 *
 * @since  1.0
 */
abstract class Controller extends AbstractController
{
	/**
	 * The default view for the app
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $defaultView;

	/**
	 * Constructor.
	 *
	 * @param   Input                $input  The input object.
	 * @param   AbstractApplication  $app    The application object.
	 *
	 * @since   1.0
	 */
	public function __construct(Input $input = null, AbstractApplication $app = null)
	{
		parent::__construct($input, $app);
	}

	/**
	 * Execute the controller.
	 *
	 * This is a generic method to execute and render a view and is not suitable for tasks.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function execute()
	{
		echo 'Executed';
		/*
		// Get the input
		$input = $this->getInput();

		// Get some data from the request
		$vName   = $input->getWord('view', $this->defaultView);
		$vFormat = $input->getWord('format', 'html');
		$lName   = $input->getCmd('layout', 'index');

		$input->set('view', $vName);

		$base   = '\\Components\\';

		$vClass = $base . '\\View\\' . ucfirst($vName) . '\\' . ucfirst($vName) . ucfirst($vFormat) . 'View';
		$mClass = $base . '\\Model\\' . ucfirst($vName) . 'Model';

		// If a model doesn't exist for our view, revert to the default model
		if (!class_exists($mClass))
		{
			$mClass = $base . '\\Model\\DefaultModel';

			// If there still isn't a class, panic.
			if (!class_exists($mClass))
			{
				throw new \RuntimeException(sprintf('No model found for view %s or a default model for %s', $vName, $this->component));
			}
		}

		// Make sure the view class exists, otherwise revert to the default
		if (!class_exists($vClass))
		{
			$vClass = '\\JTracker\\View\\TrackerDefaultView';

			// If there still isn't a class, panic.
			if (!class_exists($vClass))
			{
				throw new \RuntimeException(sprintf('Class %s not found', $vClass));
			}
		}

		// Register the templates paths for the view
		$paths = array();

		$sub = ('php' == $this->getApplication()->get('renderer.type')) ? '/php' : '';

		$path = JPATH_TEMPLATES . $sub . '/' . strtolower($this->component);

		if (is_dir($path))
		{
			$paths[] = $path;
		}

		
		$view = new $vClass(new $mClass, $paths);
		$view->setLayout($vName . '.' . $lName);

		try
		{
			// Render our view.
			echo $view->render();
		}
		catch (\Exception $e)
		{
			echo $this->getApplication()->getDebugger()
				->renderException($e);
		}

		return;
		*/
	}
}
