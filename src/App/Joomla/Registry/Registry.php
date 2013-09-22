<?php
/**
 * Part of the Joomla Standard Edition Component Package
 *
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace App\Joomla\Registry;

use Joomla\Registry\Registry as JoomlaRegistry;
use Joomla\Registry\AbstractRegistryFormat;
use Joomla\Utilities\ArrayHelper;

class Registry extends JoomlaRegistry
{
	/**
	 * Method to recursively bind data to a parent object.
	 *
	 * @param   object  $parent  The parent object on which to attach the data values.
	 * @param   mixed   $data    An array or object of data to bind to the parent object.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	protected function bindData($parent, $data)
	{
		// Ensure the input data is an array.
		if (is_object($data))
		{
			$data = get_object_vars($data);
		}
		else
		{
			$data = (array) $data;
		}

		foreach ($data as $k => $v)
		{
			if ((is_array($v) && ArrayHelper::isAssociative($v)) || is_object($v))
			{
				$parent->$k = isset($parent->$k) ? $parent->$k : new \stdClass;
				$this->bindData($parent->$k, $v);
			}
			else
			{
				$parent->$k = $v;
			}
		}
	}
	
	/**
	 * Merge a Registry object into this one
	 *
	 * @param   Registry  $source  Source Registry object to merge.
	 *
	 * @return  boolean  True on success
	 *
	 * @since   1.0
	 */
	public function merge($source)
	{
		if (!$source instanceof Registry)
		{
			return false;
		}

		$this->bindData($this->data, $source->toSrray());

		return $this;
	}
}