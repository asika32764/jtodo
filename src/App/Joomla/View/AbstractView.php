<?php
/**
 * Part of the Joomla Framework View Package
 *
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace App\Joomla\View;

use Joomla\View\ViewInterface;

/**
 * Joomla Framework Abstract View Class
 *
 * @since  1.0
 */
abstract class AbstractView implements ViewInterface
{
	/**
	 * Method to instantiate the view.
	 *
	 * @since   1.0
	 */
	public function __construct()
	{
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
		return $output;
	}
}