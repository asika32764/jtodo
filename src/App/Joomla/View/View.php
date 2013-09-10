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

//use JTracker\Model\TrackerDefaultModel;

/**
 * Default view class for the Tracker application
 *
 * @since  1.0
 */
class View  extends HtmlView
{
    
	/**
	 * Method to instantiate the view.
	 *
	 * @param   ModelInterface  $model           The model object.
	 * @param   string|array    $templatesPaths  The templates paths.
	 *
	 * @since   1.0
	 */
	public function __construct()
	{
		//$model = $model ? : new TrackerDefaultModel;
        
		parent::__construct();
	}
}
