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
        $data = $this->fetch('@Todo:Category:Add', array(
			'is_hmvc' => '123'
		))->execute()
		;
		//$this->forward('substr/category/cat/add', array(
		//	'is_hmvc' => '123'
		//));
		
		//$config = new \App\Joomla\Config\Config(null, new \App\Joomla\Component\ComponentResolver);
		
		//$config->loadFile(JPATH_APPLICATION . '/config/config_dev.json');
		
		//show($config);
		
        //show($this->container->get('config'));die;
		$this->getView()->set('submit', $data);
        
		return parent::execute();
	}
}
