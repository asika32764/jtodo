<?php
/**
 * Part of the Joomla Framework
 *
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace App\Joomla;

use Joomla\Factory as FactoryBase;

use Joomla\DI\Container;

/**
 * Joomla Framework Factory class
 *
 * @since  1.0
 */
abstract class Factory extends FactoryBase
{
    /**
	 * DI Container object instance
	 *
	 * @var    JConfig
	 * @since  1.0
	 */
	public static $container = null;
    
    /**
     * function getContainer
     */
    public static function getContainer()
    {
        if (!self::$container)
		{
			self::$container = new Container();
		}

		return self::$container;
    }
}