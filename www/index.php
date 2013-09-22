<?php
/**
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// Set error reporting for development
error_reporting(32767);

// Define base path
define('JPATH_BASE', dirname(__DIR__));

require JPATH_BASE . '/app/defines.php';

// Load the Composer autoloader
require JPATH_BASE . '/vendor/autoload.php';

// Load Application
require_once JPATH_APPLICATION . '/App/Site/Application.php';

// Load the Joomla Framework
$container = require_once JPATH_BASE . '/app/bootstrap.php';

// Execute the application.
$container->get('application')
    ->setEnvironment('prod')
    ->execute();
