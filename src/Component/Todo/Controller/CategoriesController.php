<?php
/**
 * Part of the Joomla Todo's Categories
 *
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Component\Todo\Controller;

use App\Joomla\Controller\Controller;

/**
 * Controller class to display the application configuration
 *
 * @since  1.0
 */
class CategoriesController extends Controller
{
	/**
	 * The default view for the component
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $defaultView = '';

	/**
	 * Execute the controller.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function execute()
	{
        $data = $this->container
            ->get('component.todo')
            ->getController('category', 'add', clone $this->getInput())
            ->execute()
            ;
        
        
		return parent::execute() . $data;
	}
}
