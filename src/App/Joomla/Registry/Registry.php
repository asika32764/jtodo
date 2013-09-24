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
		
		return $this;
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
	
	/**
	 * Load a associative array of values into the default namespace
	 *
	 * @param   array  $array  Associative array of value to load
	 *
	 * @return  boolean  True on success
	 *
	 * @since   1.0
	 */
	public function loadArray($array)
	{
		parent::loadArray($array);
		
		return $this;
	}

	/**
	 * Load the public variables of the object into the default namespace.
	 *
	 * @param   object  $object  The object holding the publics to load
	 *
	 * @return  boolean  True on success
	 *
	 * @since   1.0
	 */
	public function loadObject($object)
	{
		parent::loadObject($object);
		
		return $this;
	}

	/**
	 * Load the contents of a file into the registry
	 *
	 * @param   string  $file     Path to file to load
	 * @param   string  $format   Format of the file [optional: defaults to JSON]
	 * @param   array   $options  Options used by the formatter
	 *
	 * @return  boolean  True on success
	 *
	 * @since   1.0
	 */
	public function loadFile($file, $format = 'JSON', $options = array())
	{
		parent::loadFile($file, $format, $options);
		
		return $this;
	}

	/**
	 * Load a string into the registry
	 *
	 * @param   string  $data     String to load into the registry
	 * @param   string  $format   Format of the string
	 * @param   array   $options  Options used by the formatter
	 *
	 * @return  boolean  True on success
	 *
	 * @since   1.0
	 */
	public function loadString($data, $format = 'JSON', $options = array())
	{
		parent::loadString($data, $format, $options);
		
		return $this;
	}
}