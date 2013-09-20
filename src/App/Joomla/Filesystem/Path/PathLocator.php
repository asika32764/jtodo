<?php
/**
 * Part of the Joomla Framework Filesystem Package
 *
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace App\Joomla\Filesystem\Path;

use Joomla\Filesystem\Path;
use Joomla\Filesystem\Path\PathLocator as PathLocatorBase;

/**
 * A Path locator class
 *
 * @since  1.0
 */
class PathLocator extends PathLocatorBase
{
    /**
	 * Regularize path, remove not necessary elements. 
	 *
	 * @param  string  $path          A given path to regularize.
	 * @param  string  $returnString  Return string or array.
	 *
	 * @return  string|array  Regularized path.
	 *
	 * @since  1.0
	 */
	protected function regularize($path, $returnString = false)
	{
        $path = parent::regularize($path ,false);
        
		if(!empty($path[0]))
        {
            if(defined($path[0]))
            {
                $path[0] = constant($path[0]);
            }
        }
		
		// If set to return string, compact it.
		if($returnString == true)
		{
			$path = $this->compact($path);
		}
		
		return $path;
	}
}