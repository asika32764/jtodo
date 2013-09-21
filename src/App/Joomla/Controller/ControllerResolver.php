<?php
/**
 * Part of the Joomla Edition Controller Package
 *
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace App\Joomla\Controller;

use App\Joomla\Factory;

/**
 * Abstract Controller class for the Tracker Application
 *
 * @since  1.0
 */
abstract class ControllerResolver
{
    /**
     * function getController
     */
    public static function getController($name)
    {
        $container = Factory::getContainer();
        
        // @SubdirTodo:Category:Save
        $name = explode(':', $name);
        
        if(count($name) == 2)
        {
            list($component, $controller) = $name;
            $action = null;
        }
        elseif(count($name) == 3)
        {
            list($component, $controller, $action) = $name;
        }
        else
        {
            throw new \InvalidArgumentException(sprintf('Controller %s not found.', implode(':', $name)));
        }
        
        $component = strtolower(str_replace('@', '', $component));
        
        try
        {
            $component = $container->get('component.' . $component);
        }
        catch(\Exception $e)
        {
            throw new \RuntimeException(sprintf('Component % not found.', $component), null, $e);
        }
        
        $controller = $component->getController($controller, $action);
        
        return $controller;
    }
}