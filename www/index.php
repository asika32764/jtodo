<?php
/**
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// Set error reporting for development
error_reporting(32767);

// Define base path
define('JPATH_BASE', __DIR__);
require JPATH_BASE . '/app/defines.php';

// Load the Composer autoloader
require JPATH_BASE . '/vendor/autoload.php';

// Load the Joomla Framework
require JPATH_BASE . '/vendor/joomla/framework/src/import.php';

// Instantiate the application.
$application = new App\Application\TrackerApplication;

// Execute the application.
$application->execute();
