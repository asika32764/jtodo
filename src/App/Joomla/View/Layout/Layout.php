<?php
/**
 * Part of the Joomla Framework Layout Package
 *
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace App\Joomla\View\Layout;

use Joomla\View\Layout\AbstractLayout;

/**
 * Joomla Framework Base Layout class
 *
 * @since  1.0
 */
class Layout extends AbstractLayout
{
    protected $renderer;
    
    protected $paths = array();
    
    /**
     * setRenderer description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  setRendererReturn
     *
     * @since  1.0
     */
    public function setRenderer($renderer)
    {
        $this->renderer = $renderer;
    }
    
    /**
     * setRenderer description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  setRendererReturn
     *
     * @since  1.0
     */
    public function getRenderer()
    {
        return $this->renderer;
    }
    
    /**
     * Method to render the layout.
     *
     * @param   object  $data  Object which properties are used inside the layout file to build displayed output
     *
     * @return  string  The necessary HTML to display the layout
     *
     * @since   3.0
     */
    public function render($data)
    {
        $this->renderer->addPath($this->paths->toArray());
        
        return $this->getRenderer()->render($data);
    }
}