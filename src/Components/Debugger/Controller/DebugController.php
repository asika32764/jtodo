<?php
/**
 * Part of the Joomla Tracker's Debug Application
 *
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Components\Debugger\Controller;

use App\Joomla\Controller\AbstractTrackerController;

/**
 * Controller class to display the application configuration
 *
 * @since  1.0
 */
class DebugController extends AbstractTrackerController
{
	/**
	 * The default view for the component
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $defaultView = 'debug';

	/**
	 * Execute the controller.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function execute()
	{
		$this->getApplication()->getUser()->authorize('admin');

		parent::execute();
	}
}
