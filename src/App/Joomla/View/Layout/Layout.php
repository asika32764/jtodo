<?php
/**
 * Part of the Joomla Framework Layout Package
 *
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace App\Joomla\View\Layout;

use Joomla\View\Layout\AbstractLayout;
use Joomla\Filesystem\Path\PathCollection;

/**
 * Joomla Framework Base Layout class
 *
 * @since  1.0
 */
class Layout extends AbstractLayout
{
    protected $renderer;
    
    protected $paths = array();
    
    protected $name;
    
    /**
     * Method to instantiate the file-based layout.
     *
     * @param   string  $layoutId  Dot separated path to the layout file, relative to base path
     * @param   string  $basePath  Base path to use when loading layout files
     *
     * @since   1.0
     */
    public function __construct($name, RendererInterface $renderer = null, $paths = array())
    {
        $this->name = $name;
        
        $this->paths = $this->setPaths($paths);
        
        $this->renderer = $renderer;
    }
    
    /**
     * getName description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  getNameReturn
     *
     * @since  1.0
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * setPaths description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  setPathsReturn
     *
     * @since  1.0
     */
    public function setPaths($paths)
    {
        $this->paths = new PathCollection($paths);
    }
    
    /**
     * addPath description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  addPathReturn
     *
     * @since  1.0
     */
    public function addPath($path, $name = '')
    {
        $this->paths->addPath($path, $name);
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
    public function render($data = array())
    {
        $this->renderer->setTemplatesPaths($this->paths->toArray());
        
        return $this->getRenderer()->render($this->name, (array) $data->dump());
    }
}