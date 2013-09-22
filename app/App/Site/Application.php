<?php
/**
 * Part of the Joomla Standard Edition Application Package
 *
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

use App\Joomla\Application\Application as JoomlaApplication;

/**
 * Joomla web application class
 *
 * @since  1.0
 */
final class Application extends JoomlaApplication
{
    /**
     * The name of the application.
     *
     * @var    array
     * @since  1.0
     */
    protected $name = 'Site';
    
    /**
     * Initialize the configuration object.
     *
     * @return  $this  Method allows chaining
     *
     * @since   1.0
     * @throws  \RuntimeException
     */
    protected function getConfigurationFiles()
    {
        return array(
            // Config
            JPATH_CONFIGURATION . '/config.json',
            
            JPATH_CONFIGURATION . '/Site/config.json',
            
            JPATH_CONFIGURATION . '/Site/config.' . $this->getEnvironment() . '.json',
            
            
            // Extension Register
            JPATH_CONFIGURATION . '/extension.json',
            
            JPATH_CONFIGURATION . '/Site/extension.json',
            
            JPATH_CONFIGURATION . '/Site/extension.' . $this->getEnvironment() . '.json',
            
            
            // Routing
            JPATH_CONFIGURATION . '/routing.json',
            
            JPATH_CONFIGURATION . '/Site/routing.json',
            
            JPATH_CONFIGURATION . '/Site/routing.' . $this->getEnvironment() . '.json'
        );
    }
}