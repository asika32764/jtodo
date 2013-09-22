<?php
/**
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// Include Joomla framework
require JPATH_BASE . '/vendor/joomla/framework/src/import.php';

// Start DI Container
$container = new Joomla\DI\Container;

// Instantiate the application.
$container->registerServiceProvider(new Application($container));

$container->share('system.resolver.component', function($container){
    return new App\Joomla\Component\ComponentResolver($container);
});

$container->share('system.resolver.controller', function($container){
    return new App\Joomla\Controller\ControllerResolver($container);
});

return $container;