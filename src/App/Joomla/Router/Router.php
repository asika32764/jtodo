<?php
/**
 * Part of the Joomla Tracker Router Package
 *
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace App\Joomla\Router;

use Joomla\Application\AbstractApplication;
use Joomla\Controller\ControllerInterface;
use Joomla\Input\Input;
use Joomla\Router\Router as RouterBase;
use Joomla\Factory;
use App\Joomla\Router\Exception\RoutingException;

/**
 * Joomla! Tracker Router
 *
 * @since  1.0
 */
class Router extends RouterBase
{
	/**
	 * Application object to inject into controllers
	 *
	 * @var    AbstractApplication
	 * @since  1.0
	 */
	protected $app;

	/**
	 * Constructor.
	 *
	 * @param   Input                $input  An optional input object from which to derive the route.  If none
	 *                                       is given than the input from the application object will be used.
	 * @param   AbstractApplication  $app    An optional application object to inject to controllers
	 *
	 * @since   1.0
	 */
	public function __construct(Input $input = null, AbstractApplication $app = null)
	{
		parent::__construct($app->input);

		$this->app = $app;
	}

	/**
	 * Find and execute the appropriate controller based on a given route.
	 *
	 * @param   string  $route  The route string for which to find and execute a controller.
	 *
	 * @return  ControllerInterface
	 *
	 * @since   1.0
	 * @throws  RoutingException
	 */
	public function getController($route)
	{	
		try
		{
			return parent::getController($route);
		}
		catch (\InvalidArgumentException $e)
		{
			// 404
			throw new RoutingException($e->getMessage());
		}
		catch (\RuntimeException $e)
		{
			// 404
			throw new RoutingException($e->getMessage());
		}
	}


}
