<?php
/**
 * Part of the Joomla Framework Layout Package
 *
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\View\Layout;

/**
 * Joomla Framework Base Layout insterface
 *
 * @since  1.0
 */
interface LayoutInterface
{
    /**
     * Method to escape output.
     *
     * @param   string  $output  The output to escape.
     *
     * @return  string  The escaped output.
     *
     * @since   3.0
     */
    public function escape($output);
    
    /**
     * Method to render the layout.
     *
     * @param   object  $data  Object which properties are used inside the layout file to build displayed output
     *
     * @return  string  The necessary HTML to display the layout
     *
     * @since   3.0
     */
    public function render($data);
}