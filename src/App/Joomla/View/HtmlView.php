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
}
