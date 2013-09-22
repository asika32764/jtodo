<?php
/**
 * Part of the Joomla Standard Edition Application Package
 *
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace App\Joomla\Application;

use Joomla\Application\AbstractWebApplication;
use Joomla\Controller\ControllerInterface;
use Joomla\Database\DatabaseDriver;
use Joomla\Event\Dispatcher;
use Joomla\Github\Github;
use Joomla\Github\Http;
use Joomla\Http\HttpFactory;
use Joomla\Language\Language;
use Joomla\Profiler\Profiler;
use Joomla\DI\Container;
use Joomla\DI\ContainerAwareInterface;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Filesystem\Path\PathCollection;

//use App\Joomla\Authentication\Exception\AuthenticationException;
//use App\Joomla\Authentication\GitHub\GitHubUser;
//use App\Joomla\Authentication\User;
//use App\Joomla\Controller\AbstractTrackerController;
use App\Joomla\Router\Exception\RoutingException;
use App\Joomla\Router\Router;
use App\Joomla\Registry\Registry;
use App\Joomla\Factory;
use App\Joomla\Controller\ControllerResolver;


use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Joomla Tracker web application class
 *
 * @since  1.0
 */
abstract class Application extends AbstractWebApplication implements ContainerAwareInterface, ServiceProviderInterface
{
    /**
     * The Dispatcher object.
     *
     * @var    Dispatcher
     * @since  1.0
     */
    protected $dispatcher;

    /**
     * The name of the application.
     *
     * @var    array
     * @since  1.0
     */
    protected $name = null;
    
    /**
     * Environment name.
     *
     * @var    array
     * @since  1.0
     */
    protected $environment = null;
    
    /**
     * DI Container object.
     *
     * @var    object
     * @since  1.0
     */
    protected $container = null;

    /**
     * A session object.
     *
     * @var    Session
     * @since  1.0
     * @note   This has been created to avoid a conflict with the $session member var from the parent class.
     */
    private $newSession = null;

    /**
     * The User object.
     *
     * @var    User
     * @since  1.0
     */
    private $user;

    /**
     * The Project object
     *
     * @var    TrackerProject
     * @since  1.0
     */
    private $project;

    /**
     * The database driver object.
     *
     * @var    DatabaseDriver
     * @since  1.0
     */
    private $database;

    /**
     * The Language object
     *
     * @var    Language
     * @since  1.0
     */
    private $language;

    /**
     * The Profiler object
     *
     * @var    Profiler
     * @since  1.0
     */
    private $profiler;

    /**
     * Class constructor.
     *
     * @since   1.0
     */
    public function __construct(Container $container = null)
    {
        $this->container = $container ?: $this->getContainer();
        
        // Run the parent constructor
        parent::__construct();
    }
    
    /**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  Container  Returns itself to support chaining.
	 *
	 * @since   1.0
	 */
	public function register(Container $container)
    {
        $container->share('application', $this);
        
        return $this;
    }
    
    /**
     * Initialisation method.
     *
     * @return  void
     * @since   1.0
     */
    protected function initialise()
    {
        //$this->mark('application.start');
        
        //$this->getContainer()->set('profiler', $container, false, true);
        
        $this->config = new Registry;
        
        $this->set('system.name', $this->getName());
        
        Factory::$application = $this;
        Factory::$container = $this->container;
        Factory::$config = $this->config;
    }
    
    /**
     * Get the DI container.
     *
     * @return  Container
     *
     * @since   1.0
     *
     * @throws  \UnexpectedValueException May be thrown if the container has not been set.
     */
    public function getContainer()
    {
        if($this->container instanceof Container) {
            return $this->container ;
        }
        
        $this->setContainer(new Container);
        
        return $this->container ;
    }
    
    /**
     * Set the DI container.
     *
     * @param   Container  $container  The DI container.
     *
     * @since   1.0
     */
    public function setContainer(Container $container)
    {
        $this->container = $container ;
        
        return $this;
    }
    
    /**
     * System environment setter.
     *
     * @since   1.0
     */
    public function setEnvironment($env)
    {
        $this->environment = $env ;
        
        return $this;
    }
    
    /**
     * System environment getter.
     *
     * @return  string
     * @since   1.0
     */
    public function getEnvironment()
    {
        return $this->environment ;
    }

    /**
     * Method to run the Web application routines.
     *
     * @return  void
     *
     * @since   1.0
     */
    protected function doExecute()
    {
        // Load the configuration object.
        $this->loadConfiguration();
        
        // Register components
        $this->loadComponents();
        
        // Get Debugger
        $debugger = $this->container->get('component.debugger')->registerDebugger();
        
        // Register the event dispatcher
        $this->loadDispatcher();
        
        // Load the library language file
        $this->getLanguage()->load('lib_joomla', JPATH_BASE);
        
        $this->mark('application.afterInitialise');
        
        echo $this->container->get('system.resolver.component')->getName('Site/Todo');
        
        // Instantiate the router
        $router = new Router($this->input, $this, $this->container);
        
        $this->container->registerServiceProvider($router);
        
        // Get URI route from config
        $route = $this->get('uri.route');
        
        // Get base routing file
        $maps    = $this->config->get('routing');
        
        // Find base routing and component routing
        $componentName = null;
        
        foreach((array)$maps as $map)
        {
            // Set * to '' ;
            $map->pattern = ($map->pattern == '*') ? '' : $map->pattern;
            $map->pattern;
            // Add separator before route & pattern beacuse strpos() can not use empty string as params.
            $route          = '/' . $route ;
            $map->pattern   = '/' . $map->pattern;
            
            if(strpos($route, $map->pattern) !== false && !$componentName)
            {
                $componentName = $map->component ;
                
                $route = trim( substr($route, strlen($map->pattern) + 1 ), '/');
                
                break;
            }
        }
        
        // Get component from container
        $component = $this->container->get('component.' . $componentName);
        
        // Parse route
        $segments = explode('/', $route);
        
        $component->parseRoute($segments, $router);
        
        $router->setControllerPrefix('Component');
        
        $route = $route ?: '*';
        
        $controller = $router->getController($route);
        $controller->setContainer($this->container);
        
        $this->setBody( $controller->execute());
        
        return true;
    }
    
    /**
	 * Execute the application.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function execute()
	{
		// @event onBeforeExecute

		// Perform application routines.
		$this->doExecute();

		// @event onAfterExecute

		// If gzip compression is enabled in configuration and the server is compliant, compress the output.
		if ($this->get('gzip') && !ini_get('zlib.output_compression') && (ini_get('output_handler') != 'ob_gzhandler'))
		{
			$this->compress();
		}

		// @event onBeforeRespond

		// Send the application response.
		$this->respond();

		// @event onAfterRespond
	}

    /**
     * Add a profiler mark.
     *
     * @param   string  $text  The message for the mark.
     *
     * @return  $this  Method allows chaining
     *
     * @since   1.0
     */
    public function mark($text)
    {
        if (JDEBUG)
        {
            $this->getProfiler()->mark($text);
        }

        return $this;
    }

    /**
     * Initialize the configuration object.
     *
     * @return  $this  Method allows chaining
     *
     * @since   1.0
     * @throws  \RuntimeException
     */
    protected function loadConfiguration()
    {
        $configs = new PathCollection($this->getConfigurationFiles());
        
        $files = array();
        
        foreach($configs as $config)
        {
            if(!$config->isFile())
            {
                throw new \RuntimeException('Config not found: ' . $config);
            }
            
            $this->config->loadFile((string) $config);
        }
        
        $this->container->share('config', $this->config);
        
        define('JDEBUG', $this->config->get('system.debug'));
        
        return $this;
    }
    
    /**
     * getConfigurationFiles description
     *
     * @param  string
     * @param  string
     * @param  string
     *
     * @return  string  getConfigurationFilesReturn
     *
     * @since  1.0
     */
    abstract protected function getConfigurationFiles();
    
    /**
     * Initialize the components and set them into container.
     *
     * @return  $this  Method allows chaining
     *
     * @since   1.0
     * @throws  \RuntimeException
     */
    private function loadComponents()
    {
        // Get system environment name.
        $type = $this->getEnvironment();
        
        $components = $this->config->get('component');
        
        // load components into DI Container
        $container = $this->getContainer();
        
        $resolver = $container->get('system.resolver.component');
        
        // $resolver->setPrefix($resolver->getPrefix() . '\\' . $this->getName());
        
        foreach($components as $key => $name)
        {
            $resolver->loadComponent($key, $name);
        }
        
        return $this;
    }

    /**
     * Enqueue a system message.
     *
     * @param   string  $msg   The message to enqueue.
     * @param   string  $type  The message type. Default is message.
     *
     * @return  $this  Method allows chaining
     *
     * @since   1.0
     */
    public function enqueueMessage($msg, $type = 'message')
    {
        $this->getSession()->getFlashBag()->add($type, $msg);

        return $this;
    }

    /**
     * Execute the component.
     *
     * @param   ControllerInterface  $controller  The controller instance to execute
     * @param   string               $component   The component being executed.
     *
     * @return  string
     *
     * @since   1.0
     * @throws  \Exception
     */
    /*
    public function executeComponent($controller, $component)
    {
        // Load template language files.
        $lang = $this->getLanguage();

        // Load common and local language files.
        $lang->load($component, JPATH_BASE, null, false, false) || $lang->load($component, JPATH_BASE, $lang->getDefault(), false, false);

        // Start an output buffer.
        ob_start();
        $controller->execute();

        return ob_get_clean();
    }
    */

    /**
     * Get a session object.
     *
     * @return  Session
     *
     * @since   1.0
     */
    public function getSession()
    {
        if (is_null($this->newSession))
        {
            $this->newSession = new Session;
            $this->newSession->start();

            // @todo Decouple from Factory
            Factory::$session = $this->newSession;
        }

        return $this->newSession;
    }
    
    /**
     * Get a Profiler object.
     *
     * @return  Profiler
     *
     * @since   1.0
     */
    public function getProfiler()
    {
        if(!$this->profiler)
        {
            $this->profiler = new Profiler('Application');
        }
        
        return $this->profiler;
    }

    /**
     * Get a database driver object.
     *
     * @return  DatabaseDriver
     *
     * @since   1.0
     */
    public function getDatabase()
    {
        if (is_null($this->database))
        {
            $this->database = DatabaseDriver::getInstance(
                array(
                    'driver' => $this->get('database.driver'),
                    'host' => $this->get('database.host'),
                    'user' => $this->get('database.user'),
                    'password' => $this->get('database.password'),
                    'database' => $this->get('database.name'),
                    'prefix' => $this->get('database.prefix')
                )
            );

            // @todo Decouple from Factory
            Factory::$database = $this->database;
        }

        return $this->database;
    }

    /**
     * Get a language object.
     *
     * @return  Language
     *
     * @since   1.0
     */
    public function getLanguage()
    {
        if (is_null($this->language))
        {
            $this->language = Language::getInstance(
                $this->get('language'),
                $this->get('debug_lang')
            );
        }

        return $this->language;
    }

    /**
     * Clear the system message queue.
     *
     * @return  void
     *
     * @since   1.0
     */
    public function clearMessageQueue()
    {
        $this->getSession()->getFlashBag()->clear();
    }

    /**
     * Get the system message queue.
     *
     * @return  array  The system message queue.
     *
     * @since   1.0
     */
    public function getMessageQueue()
    {
        return $this->getSession()->getFlashBag()->peekAll();
    }

    /**
     * Set the system message queue for a given type.
     *
     * @param   string  $type     The type of message to set
     * @param   mixed   $message  Either a single message or an array of messages
     *
     * @return  void
     *
     * @since   1.0
     */
    public function setMessageQueue($type, $message = '')
    {
        $this->getSession()->getFlashBag()->set($type, $message);
    }

    /**
     * Method to get the application name.
     *
     * The dispatcher name is by default parsed using the class name, or it can be set
     * by passing a $config['name'] in the class constructor.
     *
     * @return  string  The name of the dispatcher.
     *
     * @since   1.0
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Gets a user state.
     *
     * @param   string  $key      The path of the state.
     * @param   mixed   $default  Optional default value, returned if the internal value is null.
     *
     * @return  mixed  The user state or null.
     *
     * @since   1.0
     */
    public function getUserState($key, $default = null)
    {
        /* @type Registry $registry */
        $registry = $this->getSession()->get('registry');

        if (!is_null($registry))
        {
            return $registry->get($key, $default);
        }

        return $default;
    }

    /**
     * Gets the value of a user state variable.
     *
     * @param   string  $key      The key of the user state variable.
     * @param   string  $request  The name of the variable passed in a request.
     * @param   string  $default  The default value for the variable if not found. Optional.
     * @param   string  $type     Filter for the variable, for valid values see {@link JFilterInput::clean()}. Optional.
     *
     * @return  mixed The request user state.
     *
     * @since   1.0
     */
    public function getUserStateFromRequest($key, $request, $default = null, $type = 'none')
    {
        $cur_state = $this->getUserState($key, $default);
        $new_state = $this->input->get($request, null, $type);

        // Save the new value only if it was set in this request.
        if ($new_state !== null)
        {
            $this->setUserState($key, $new_state);
        }
        else
        {
            $new_state = $cur_state;
        }

        return $new_state;
    }

    /**
     * Sets the value of a user state variable.
     *
     * @param   string  $key    The path of the state.
     * @param   string  $value  The value of the variable.
     *
     * @return  mixed  The previous state, if one existed.
     *
     * @since   1.0
     */
    public function setUserState($key, $value)
    {
        /* @type Registry $registry */
        $registry = $this->getSession()->get('registry');

        if (!is_null($registry))
        {
            return $registry->set($key, $value);
        }

        return null;
    }

    /**
     * Allows the application to load a custom or default dispatcher.
     *
     * The logic and options for creating this object are adequately generic for default cases
     * but for many applications it will make sense to override this method and create event
     * dispatchers, if required, based on more specific needs.
     *
     * @param   Dispatcher  $dispatcher  An optional dispatcher object. If omitted, the factory dispatcher is created.
     *
     * @return  $this  Method allows chaining
     *
     * @since   1.0
     */
    public function loadDispatcher(Dispatcher $dispatcher = null)
    {
        $this->dispatcher = ($dispatcher === null) ? new Dispatcher : $dispatcher;

        return $this;
    }
}
