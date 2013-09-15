<?php
/**
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// Set error reporting for development
error_reporting(32767);

/**
 * Print Array or Object as tree node. If send multiple params in this method, will batch print it.
 * 
 * @param    mixed    $data    Array or Object to print.
 */
function show($data)
{
    $args   = func_get_args();
    
    // Print Multiple values
    if(count($args) > 1) {    
        $prints = array();
        
        $i = 1 ;
        foreach( $args as $arg ):
            $prints[] = "[Value " . $i . "]\n" . print_r($arg, 1);
            $i++ ;
        endforeach;
        
        echo '<pre>'.implode("\n\n", $prints).'</pre>' ;
    }else{
        // Print one value.
        echo '<pre>'.print_r($data, 1).'</pre>' ;
    }        
}

// Define base path
define('JPATH_BASE', dirname(__DIR__));
require JPATH_BASE . '/app/defines.php';

// Load the Composer autoloader
require JPATH_BASE . '/vendor/autoload.php';

// Load the Joomla Framework
require JPATH_BASE . '/vendor/joomla/framework/src/import.php';

// Instantiate the application.
$application = new App\Joomla\Application\Application;

// Execute the application.
$application->execute();
