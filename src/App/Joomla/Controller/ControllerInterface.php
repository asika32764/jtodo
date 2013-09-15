<?php

/**
 * Part of the Joomla Edition Controller Package
 *
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace App\Joomla\Controller;
 
interface ControllerInterface
{
    /**
     * function getDefaultView
     */
    function getDefaultView();
    
    /**
     * Method to get the controller name
     *
     * @return  string  The name of the dispatcher
     *
     * @since   1.0
     */
    function getName();
    
    /**
     * Method to get a reference to the current view and load it if necessary.
     *
     * @param   string  $name    The view name. Optional, defaults to the controller name.
     * @param   string  $type    The view type. Optional.
     * @param   string  $prefix  The class prefix. Optional.
     * @param   array   $config  Configuration array for view. Optional. This param
     *                           copy from CMS but not used now.
     *
     * @return  object  Reference to the view or an error.
     *
     * @since   1.0
     */
    function getView($name = '', $type = '', $nameSpace = '', $config = array());
    
    /**
     * Method to load and return a view object. This method first looks in the
     * current template directory for a match and, failing that, uses a default
     * set path to load the view class file.
     *
     * @param   string  $name       The name of the view.
     * @param   string  $nameSpace  Optional prefix for the view class name.
     * @param   string  $type       The type of view.
     * @param   array   $config     Configuration array for the view. Optional.
     *
     * @return  mixed  View object on success; null or error result on failure.
     *
     * @since   1.0
     */
    function createView($name, $nameSpace = '', $type = '', $config = array());
    
    /**
     * Method to get a model object, loading it if required.
     *
     * @param   string  $name    The model name. Optional.
     * @param   string  $prefix  The class prefix. Optional.
     * @param   array   $config  Configuration array for model. Optional.
     *
     * @return  object  The model.
     *
     * @since   1.0
     */
    function getModel($name = '', $nameSpace = '', $config = array());
    
    /**
     * Method to load and return a model object.
     *
     * @param   string  $name    The name of the model.
     * @param   string  $prefix  Optional model prefix.
     * @param   array   $config  Configuration array for the model. Optional.
     *
     * @return  mixed   Model object on success; otherwise null failure.
     *
     * @since   1.0
     */
    function createModel($name, $nameSpace = '', $config = array());
    
    /**
     * Method to get the controller namespace
     *
     * @return  string  The namespace of component
     *
     * @since   1.0
     */
    function getNamespace();
    
    /**
     * function getReflection
     */
    function getReflection();
}
