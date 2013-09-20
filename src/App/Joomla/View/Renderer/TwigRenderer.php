<?php
/**
 * @package    JTracker\View\Renderer
 *
 * @copyright  Copyright (C) 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */
 
namespace App\Joomla\View\Renderer;
 
use Joomla\Registry\Registry;
 
use App\Joomla\View\Renderer\Twig\FilesystemLoader;
use App\Joomla\View\Renderer\RendererInterface;
 
/**
 * Twig class for rendering output.
 *
 * @since  1.0
 */
class TwigRenderer extends \Twig_Environment implements RendererInterface
{
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
 
	/**
	 * The data for the renderer.
	 *
	 * @var    array
	 * @since  1.0
	 */
	private $data = array();
 
	/**
	 * The templates location paths.
	 *
	 * @var    array
	 * @since  1.0
	 */
	private $templatesPaths = array();
 
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
	public function __construct(TwigAppExtension $extension)
	{
        $this->config = new Registry($this->config);
        
		if (!$this->config->get('environment.debug'))
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
 
		parent::__construct($this->twigLoader, $this->config->get('environment'));
        
        $this->addExtension(new $extension);
	}
 
	/**
	 * Get the Lexer instance.
	 *
	 * @return  \Twig_LexerInterface  A Twig_LexerInterface instance.
	 *
	 * @since   1.0
	 */
    /*
	public function getLexer()
	{
		if (null === $this->lexer)
		{
			$this->lexer = new \Twig_Lexer($this, array(
                'tag_comment'    => array('{#', '#}'),
                'tag_block'      => array('{%', '%}'),
                'tag_variable'   => array('{{', '}}')
            ));
		}
 
		return $this->lexer;
	}
	*/
 
	/**
	 * Set the data for the renderer.
	 *
	 * @param   mixed    $key     The variable name or an array of variable names with values.
	 * @param   mixed    $value   The value.
	 * @param   boolean  $global  Is this a global variable?
	 *
	 * @return  Twig  Method supports chaining.
	 *
	 * @since   1.0
	 * @throws  \InvalidArgumentException
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
			if (!isset($value))
			{
				throw new \InvalidArgumentException('No value defined.');
			}
 
			if ($global)
			{
				$this->addGlobal($key, $value);
			}
			else
			{
				$this->data[$key] = $value;
			}
		}
 
		return $this;
	}
 
	/**
	 * Unset a particular variable.
	 *
	 * @param   mixed  $key  The variable name.
	 *
	 * @return  Twig  Method supports chaining.
	 *
	 * @since   1.0
	 */
	public function unsetData($key)
	{
		return $this->unsetData($key);
	}
 
	/**
	 * Render and return compiled HTML.
	 *
	 * @param   string  $template  The template file name.
	 * @param   array   $data      An array of data to pass to the template.
	 *
	 * @return  string  Compiled HTML.
	 *
	 * @since   1.0
	 * @throws  \RuntimeException
	 */
	public function render($template = '', array $data = array())
	{
		if (!empty($template))
		{
			$this->setTemplate($template);
		}
 
		if (!empty($data))
		{
			$this->set($data);
		}
 
		try
		{
			return $this->load()->render($this->data);
		}
		catch (\Twig_Error_Loader $e)
		{
			throw new \RuntimeException($e->getRawMessage());
		}
	}
 
	/**
	 * Display the compiled HTML content.
	 *
	 * @param   string  $template  The template file name.
	 * @param   array   $data      An array of data to pass to the template.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function display($template = '', array $data = array())
	{
		if (!empty($template))
		{
			$this->setTemplate($template);
		}
 
		if (!empty($data))
		{
			$this->set($data);
		}
 
		try
		{
			$this->load()->display($this->data);
		}
		catch (\Twig_Error_Loader $e)
		{
			echo $e->getRawMessage();
		}
	}
 
	/**
	 * Get the current template name.
	 *
	 * @return  string  The name of the currently loaded template file (without the extension).
	 *
	 * @since   1.0
	 */
	public function getTemplate()
	{
		return $this->template;
	}
 
	/**
	 * Add a path to the templates location array.
	 *
	 * @param   string  $path  Templates location path.
	 *
	 * @return  $this
	 *
	 * @since   1.0
	 */
	public function addPath($path, $name = null)
	{
		try
        {
            $this->twigLoader->addPath($path, $name);
        }
        catch (\Twig_Error_Loader $e)
        {
            echo $e->getRawMessage();
        }
        
        return $this;
	}
 
	/**
	 * Set the template.
	 *
	 * @param   string  $name  The name of the template file.
	 *
	 * @return  Twig  Method supports chaining.
	 *
	 * @since   1.0
	 */
	public function setTemplate($name)
	{
		$this->template = $name;
 
		return $this;
	}
 
	/**
	 * Sets the paths where templates are stored.
	 *
	 * @param   string|array  $paths            A path or an array of paths where to look for templates.
	 * @param   bool          $overrideBaseDir  If true a path can be outside themes base directory.
	 *
	 * @return  Twig
	 *
	 * @since   1.0
	 */
	public function setTemplatesPaths($paths, $overrideBaseDir = true)
	{
		// Reset the paths if needed.
		if (is_object($this->twigLoader))
		{
            foreach($paths as $key => $path)
            {
                $this->addPath($path, $key);
            }
            
            $this->twigLoader->setPaths(array($paths['Self']));
		}
 
		return $this;
	}
 
	/**
	 * Load the template and return an output object.
	 *
	 * @return  object  Output object.
	 *
	 * @since   1.0
	 */
	private function load()
	{
		return $this->loadTemplate($this->getTemplate() . $this->config['template_file_ext']);
	}
}