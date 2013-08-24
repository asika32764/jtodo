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
use Joomla\Registry\Registry;
use Joomla\Profiler\Profiler;
use Joomla\DI\Container;
use Joomla\DI\ContainerAwareInterface;

//use App\Joomla\Authentication\Exception\AuthenticationException;
//use App\Joomla\Authentication\GitHub\GitHubUser;
//use App\Joomla\Authentication\User;
//use App\Joomla\Controller\AbstractTrackerController;
use App\Joomla\Router\Exception\RoutingException;
use App\Joomla\Router\Router;
use App\Joomla\Factory;

use Symfony\Component\HttpFoundation\Session\Session;

/**
 * Joomla Tracker web application class
 *
 * @since  1.0
 */
final class Application extends AbstractWebApplication implements ContainerAwareInterface
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
    public function __construct()
    {
        // Run the parent constructor
        parent::__construct();
        
        $this->mark('application.start');
        $this->getContainer()->set('profiler', $this->getProfiler(), false, true);
        
        // Register the application to Factory
        // @todo Decouple from Factory
        Factory::$application = $this;
        Factory::$container = $this->getContainer() ;
    }
    
    /**
     * Initialisation method.
     *
     * @return  void
     * @since   1.0
     */
    protected function initialise()
    {
        // Load the configuration object.
        $this->loadConfiguration();
        
        // Register the config to Factory
        Factory::$config = $this->config;
        
        // Register components
        $this->loadComponents();
        
        // Register the event dispatcher
        $this->loadDispatcher();
        
        // Load the library language file
        $this->getLanguage()->load('lib_joomla', JPATH_BASE);
        
        $this->mark('application.afterInitialise');
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
        if($this->container && $this->container instanceof Container) {
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
    }
    
    /**
     * System environment setter.
     *
     * @since   1.0
     */
    public function setEnvironment($env)
    {
        $this->environment = $env ;
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
        try
        {
            // Load Components
            
            
            // Instantiate the router
            $router = new Router($this->input, $this);
            $this->container->set('system.router', $router);
            
            $route = $this->get('uri.route');
            
            $segments = explode('/', $route);
            $componentName = array_shift($segments);
            $component = $this->container->get('component.' . $componentName);
            
            
            
            
            
            $maps = json_decode(file_get_contents(JPATH_CONFIGURATION . '/routes.json'));
            
            if (!$maps)
            {
                throw new \RuntimeException('Invalid router file.', 500);
            }
            
            $router->addMaps($maps, true);
            $component->parseRoute($segments);
            $router->setControllerPrefix('Components');
            
            $controller = $router->getController(implode('/', $segments));
            //show($controller);
            echo $controller->execute();
            
            
            //$router->addMap($maps->_default->pattern, true);
            
            /*
            //$router->addMaps($maps, true);
            $router->setControllerPrefix('\\Components');
            $router->setDefaultController('\\Todo\\Controller\\CategoriesController');
            
            // Fetch the controller
            
            $controller = $router->getController($this->get('uri.route'));

            // Define the app path
            define('JPATH_APP', JPATH_BASE . '/src/App/' . ucfirst($controller->getComponent()));

            // Execute the component
            $contents = $this->executeComponent($controller, strtolower($controller->getComponent()));

            $this->mark('Application terminated');

            $contents = str_replace('%%%DEBUG%%%', $this->debugger->getOutput(), $contents);

            $this->setBody($contents);
            */
        }
        catch (AuthenticationException $exception)
        {
            header('HTTP/1.1 403 Forbidden', true, 403);

            $this->mark('Application terminated with an AUTH EXCEPTION');

            $context = array();
            $context['message'] = 'Authentication failure';

            if (JDEBUG)
            {
                // The exceptions contains the User object and the action.
                if ($exception->getUser()->username)
                {
                    $context['user'] = $exception->getUser()->username;
                    $context['id'] = $exception->getUser()->id;
                }

                $context['action'] = $exception->getAction();
            }
throw new \RuntimeException("Authentication failure.");
            $this->setBody($this->container->get('component.debugger')->renderException($exception, $context));
        }
        catch (RoutingException $exception)
        {
            header('HTTP/1.1 404 Not Found', true, 404);

            $this->mark('Application terminated with a ROUTING EXCEPTION');

            $context = JDEBUG ? array('message' => $exception->getRawRoute()) : array();
            throw new \RuntimeException("HTTP/1.1 404 Not Found.");
            $this->setBody($this->debugger->renderException($exception, $context));
        }
        catch (\InvalidArgumentException $exception)
        {
            throw new \RuntimeException("Component {$componentName} not found.");
        }
        catch (\Exception $exception)
        {
            header('HTTP/1.1 500 Internal Server Error', true, 500);

            $this->mark('Application terminated with an EXCEPTION');
            throw new \RuntimeException("HTTP/1.1 500 Internal Server Error");
            $this->setBody($this->container->get('component.debugger')->renderException($exception));
        }
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
    private function loadConfiguration()
    {
        // Check for a custom configuration.
        $type = $this->getEnvironment();

        $name = ($type) ? 'config.' . $type : 'config';

        // Find the configuration file.
        foreach( new \FilesystemIterator(JPATH_CONFIGURATION) as $file ) :
        
            $fileName = $file->getFileName();
            
            if(strpos($fileName, $name) !== false) {
                
                // Verify the configuration exists and is readable.
                if(!$file->isReadable())
                {
                    throw new \RuntimeException('Configuration file does not exist or is unreadable.');
                }
                
                // Load the configuration file into Registry.
                $path   = $file->getRealPath();
                $ext    = $file->getExtension();
                $result = $this->config->loadFile($path, $ext);
                
                if (!$result)
                {
                    throw new \RuntimeException(sprintf('Unable to parse the configuration file %s.', $file));
                }
                
                if(!$this->config->get('system.config_type'))
                {
                    $this->config->set('system.config_type', $ext);
                }
                
                break;
            }
            
        endforeach;

        define('JDEBUG', $this->get('debug.system'));
        
        return $this;
    }
    
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
        
        // Set the component registration file name & path.
        $name = ($type) ? 'components.' . $type : 'components';
        
        $filepath = JPATH_CONFIGURATION . '/' . $name . '.' . $this->config->get('system.config_type') ;
        
        // Load file.
        $file = new \SplFileObject( $filepath );
        
        // Verify the file exists and is readable.
        if(!$file->isReadable())
        {
            throw new \RuntimeException('Component registration file does not exist or is unreadable.');
        }
        
        $components = json_decode(file_get_contents($filepath));
        
        if ($components === null)
        {
            throw new \RuntimeException(sprintf('Unable to parse the component registration file %s.', $filepath));
        }
        
        $this->config->set('component', $components);
        
        
        
        // load components into DI Container
        $container = $this->getContainer();
        
        foreach($components as $key => $name)
        {
            $class = 'Components\\' . ucfirst($name) . '\\' . ucfirst($name) . 'Component' ;
            
            // Check for the requested controller.
            if (!class_exists($class) /*|| !is_subclass_of($class, 'Joomla\\App\\Component\\ComponentInterface')*/)
            {
                throw new \RuntimeException($class.' not found');
            }
            
            new $class($this, $container, $key);
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

    /**
     * Provides a secure hash based on a seed
     *
     * @param   string  $seed  Seed string.
     *
     * @return  string  A secure hash
     *
     * @since   1.0
     */
    public static function getHash($seed)
    {
        return md5(Factory::getConfig()->get('acl.secret') . $seed);
    }

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
     * Get a user object.
     *
     * @param   integer  $id  The user id or the current user.
     *
     * @return  User
     *
     * @since   1.0
     */
    public function getUser($id = 0)
    {
        if ($id)
        {
            return new GitHubUser($id);
        }

        if (is_null($this->user))
        {
            $this->user = ($this->getSession()->get('user'))
                ? : new GitHubUser;
        }

        return $this->user;
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
     * Login or logout a user.
     *
     * @param   User  $user  The user object.
     *
     * @return  $this  Method allows chaining
     *
     * @since   1.0
     */
    public function setUser(User $user = null)
    {
        if (is_null($user))
        {
            // Logout
            $this->user = new GitHubUser;

            $this->getSession()->set('user', $this->user);

            // @todo cleanup more ?
        }
        else
        {
            // Login
            $user->isAdmin = in_array($user->username, $this->get('acl.admin_users'));

            $this->user = $user;

            $this->getSession()->set('user', $user);
        }

        return $this;
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
