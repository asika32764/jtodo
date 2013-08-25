<?php
/**
 * Part of the Joomla Standard Edition Component Package
 *
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace App\Joomla\Component;

use Joomla\Router\Router;

interface ComponentInterface
{
    /**
     * Get component name.
     *
     * @return  string
     * 
     * @since 1.0
     */
    public function getName();
    
    /**
     * Set component name.
     *
     * @return  string
     * 
     * @since 1.0
     */
    public function setName($name);
    
    /**
     * Parse uri segments as route.
     *
     * @param   string  $segments   URI segments.
     *
     * @return  array   Query vars.
     *
     * @since   1.0
     */
    public function parseRoute($segments, Router $router);
    
    /**
     * Build query to uri.
     *
     * @param   string  $query   URL query.
     *
     * @return  array   Uri segments.
     *
     * @since   1.0
     */
    public function buildRoute($query, Router $router);
    
    /**
     * function getPath
     */
    public function getPath();
    
    /**
     * function setDefaultController
     */
    public function setDefaultController($name);
    
    /**
     * function getDefaultController
     */
    public function getDefaultController();
}