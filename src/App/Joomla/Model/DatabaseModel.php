<?php
/**
 * Part of the Joomla Tracker View Package
 *
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace App\Joomla\Model;

use Joomla\Factory;
use Joomla\Language\Text;
use Joomla\Model\AbstractModel;
use Joomla\Model\ModelInterface;

use App\Joomla\Application\Application;

/**
 * Abstract Model
 *
 * @since  1.0
 */
abstract class DatabaseModel extends AbstractDatabaseModel
{
    /**
	 * The model (base) name
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $name = null;

	/**
	 * The URL option for the component.
	 *
	 * @var    string
	 * @since  1.0
	 */
	protected $option = null;

	/**
	 * Table instance
	 *
	 * @var    AbstractDatabaseTable
	 * @since  1.0
	 */
	protected $table;

	/**
	 * Instantiate the model.
	 *
	 * @param   DatabaseDriver  $database  The database adapter.
	 *
	 * @since   1.0
	 */
	public function __construct(DatabaseDriver $database = null)
	{
		$database = (is_null($database)) ? Factory::$application->getDatabase() : $database;

		parent::__construct($database);

		// Guess the option from the class name (Option)Model(View).
		if (empty($this->option))
		{
			// Get the fully qualified class name for the current object
			$fqcn = (get_class($this));

			// Strip the base component namespace off
			$className = str_replace('App\\', '', $fqcn);

			// Explode the remaining name into an array
			$classArray = explode('\\', $className);

			// Set the component as the first object in this array
			$this->component = $classArray[0];
		}

		// Set the view name
		if (empty($this->name))
		{
			$this->getName();
		}
	}

	/**
	 * Method to get the model name
	 *
	 * The model name. By default parsed using the class name or it can be set
	 * by passing a $config['name'] in the class constructor
	 *
	 * @return  string  The name of the model
	 *
	 * @since   1.0
	 */
	public function getName()
	{
		if (empty($this->name))
		{
			// Get the fully qualified class name for the current object
			$fqcn = (get_class($this));

			// Explode the name into an array
			$classArray = explode('\\', $fqcn);

			// Get the last element from the array
			$class = array_pop($classArray);

			// Remove Model from the name and store it
			$this->name = str_replace('Model', '', $class);
		}

		return $this->name;
	}
}