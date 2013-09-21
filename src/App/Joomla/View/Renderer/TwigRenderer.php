<?php
/**
 * @package    JTracker\View\Renderer
 *
 * @copyright  Copyright (C) 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
 
namespace App\Joomla\View\Renderer;
 
use Joomla\Registry\Registry;
use Joomla\Data\DataObject;
use Joomla\Filesystem\Path\PathCollection;
 
use App\Joomla\View\Renderer\Twig\FilesystemLoader;
use App\Joomla\View\Renderer\RendererInterface;

 
/**
 * Twig class for rendering output.
 *
 * @since  1.0
 */
class TwigRenderer implements RendererInterface
{
    const PASS_AS_GLOBAL = true;
    
    /**
	 * The renderer default configuration parameters.
	 *
	 * @var    array
	 * @since  1.0
	 */
	private $config = array(
		'template_file_ext'  => '.twig',
		'twig_cache_dir'     => 'cache/twig/',
		'environment'        => array()
	);
    
    private $twig = array();
 
	/**
	 * The data for the renderer.
	 *
	 * @var    array
	 * @since  1.0
	 */
	private $data = array();
    
    /**
	 * The data for the renderer.
	 *
	 * @var    array
	 * @since  1.0
	 */
	private $global = array();
 
	/**
	 * The templates location paths.
	 *
	 * @var    array
	 * @since  1.0
	 */
	private $templatePaths = array();
 
	/**
	 * Current template name.
	 *
	 * @var    string
	 * @since  1.0
	 */
	private $template;
 
	/**
	 * Loads template from the filesystem.
	 *
	 * @var    \Twig_Loader_Filesystem
	 * @since  1.0
	 */
	private $twigLoader;
 
	/**
	 * Instantiate the renderer.
	 *
	 * @param   array  $config  The array of configuration parameters.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function __construct(TwigAppExtension $extension, DataObject $data = null, PathCollection $paths = null)
	{
        $this->extension = $extension;
        
        $this->data = $data;
        
        $this->templatePaths = $paths;
        
        $this->global = new DataObject();
	}
    
    /**
	 * Render and return compiled HTML.
	 *
	 * @param   string  $template  The template file name
	 * @param   mixed   $data      The data to pass to the template
	 *
	 * @return  string  compiled HTML
	 *
	 * @since   1.0
	 */
	public function render($template = '', array $data = array())
    {
        $twig     = $this->getTwig();
        $loader   = $twig->getLoader();
        
        // Prepare data
        $template = $template ?: $this->template;
        $this->set($data);
        
        // Set templates
        $paths    = $this->templatePaths;
        $basepath = $paths[key($paths)];
        
        // Add paths
        $loader->addPath($basepath);
        
        foreach($paths as $key => $path)
        {
            $loader->addPath((string) $path, $key);
        }
        
        try
		{
			return $twig->render($template . $this->config->get('template_file_ext'), (array) $this->data->dump());
		}
		catch (\Twig_Error_Loader $e)
		{
			throw new \RuntimeException($e->getRawMessage());
		}
    }

	/**
	 * Set the template.
	 *
	 * @param   string  $name  The name of the template file.
	 *
	 * @return  RendererInterface
	 *
	 * @since   1.0
	 */
	public function setTemplate($name)
    {
        $this->template = $name;
    }

	/**
	 * Sets the paths where templates are stored.
	 *
	 * @param   string|array  $paths            A path or an array of paths where to look for templates.
	 * @param   bool          $overrideBaseDir  If true a path can be outside themes base directory.
	 *
	 * @return  RendererInterface
	 *
	 * @since   1.0
	 */
	public function setPaths($paths, $overrideBaseDir = false)
    {
        $paths = (array) $paths;
        
        $this->templatePaths = new PathCollection($paths);
        
        if($overrideBaseDir)
        {
            $this->templatePaths->setPrefix($this->config->get('template_base_dir'));
        }
        
        return $this;
    }

	/**
	 * Set the templates location paths.
	 *
	 * @param   string  $path  Templates location path.
	 *
	 * @return  RendererInterface
	 *
	 * @since   1.0
	 */
	public function addPath($path, $key = null)
    {
        $this->templatePaths->addPath($path, $name);
        
        return $this;
    }

	/**
	 * Set the data.
	 *
	 * @param   mixed  $key    The variable name or an array of variable names with values.
	 * @param   mixed  $value  The value.
	 *
	 * @return  RendererInterface
	 *
	 * @since   1.0
	 */
	public function set($key, $value = null, $global = false)
    {
        if (is_array($key))
		{
			foreach ($key as $k => $v)
			{
				$this->set($k, $v, $global);
			}
		}
		else
		{
			if (!$value)
			{
				throw new \InvalidArgumentException('No value defined.');
			}
 
			if ($global)
			{
				$this->getTwig()->addGlobal($key, $value);
			}
			else
			{
				$this->data->$key = $value;
			}
		}
        
        return $this;
    }

	/**
	 * Unset a particular variable.
	 *
	 * @param   mixed  $key  The variable name
	 *
	 * @return  RendererInterface
	 *
	 * @since   1.0
	 */
	public function unsetData($key)
    {
        unset($this->data->$key);
        
        return $this;
    }
    
    /**
     * loadTwig description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  loadTwigReturn
     *
     * @since  1.0
     */
    protected function getTwig()
    {
        if($this->twig)
        {
            return $this->twig;
        }
        
        $this->config = new Registry($this->config);
        
        if ($this->config->get('environment.debug'))
		{
			$this->addExtension(new \Twig_Extension_Debug);
		}
        
        try
		{
			$this->twigLoader = new FilesystemLoader();
		}
		catch (\Twig_Error_Loader $e)
		{
			throw new \RuntimeException($e->getRawMessage());
		}
        
        $twig = new \Twig_Environment($this->twigLoader, $this->config->get('environment'));
        
        if (!$this->config->get('environment.debug'))
		{
			$twig->addExtension(new \Twig_Extension_Debug);
		}
        
        $twig->addExtension(new $this->extension);
        
        return $twig;
    }
}