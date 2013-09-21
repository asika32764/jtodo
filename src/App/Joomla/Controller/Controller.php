<?php
/**
 * Part of the Joomla Edition Controller Package
 *
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace App\Joomla\Controller;

use Joomla\Factory;
use Joomla\Application\AbstractApplication;
use Joomla\Controller\AbstractController;
use Joomla\Input\Input;
use Joomla\Log\Log;
use Joomla\Filesystem\Path;
use Joomla\DI\Container;
use Joomla\DI\ContainerAwareInterface;

//use App\Joomla\Controller\ControllerInterface;
//use JTracker\Application\TrackerApplication;
//use JTracker\View\AbstractTrackerHtmlView;

/**
 * Abstract Controller class for the Tracker Application
 *
 * @since  1.0
 */
abstract class Controller extends AbstractController implements ContainerAwareInterface//ControllerInterface
{
    /** 
     * The default view for the app
     *
     * @var    string
     * @since  1.0
     */
    protected $defaultView;
    
    /**
     * The Component Name.
     *
     * @var    string
     * @since  1.0
     */
    protected $component;
    
    /**
     * The Component namespace prefix of every classes.
     *
     * @var    string
     * @since  1.0
     */
    protected $nameSpace = null;
    
    /**
     * The name of the controller
     *
     * @var    array 
     * @since  1.0
     */
    protected $name;
    
    /**
     * The base path of the controller
     *
     * @var    string 
     * @since  1.0
     */
    protected $path;
    
    /**
     * Redirect message.
     *
     * @var    string 
     * @since  1.0
     */
    protected $message;
 
    /**
     * Redirect message type.
     *
     * @var    string 
     * @since  1.0
     */
    protected $messageType;
    
    /**
     * Constructor.
     *
     * @param   Input                $input  The input object.
     * @param   AbstractApplication  $app    The application object.
     *
     * @since   1.0
     */
    public function __construct(Input $input = null, AbstractApplication $app = null)
    {
        if(!$app)
        {
            $app = Factory::$application;
        }
        
        parent::__construct($input, $app);
    }

    /**
     * Execute the controller.
     *
     * This is a generic method to execute and render a view and is not suitable for tasks.
     *
     * @return  void
     *
     * @since   1.0
     * @throws  \RuntimeException
     */
    public function execute()
    {
        // Get the input
        $input = $this->getInput();

        // Get some data from the request
        $vName   = $input->getWord('view', $this->getDefaultView());
        $vFormat = $input->getWord('format', 'Html');
        $lName   = $input->getCmd('layout', 'default');

        $input->set('view', $vName);
        
        $view = $this->getView($vName, $vFormat);
        
        $view->setLayout($lName);
        
        $model = $this->getModel();
        
        return $view->render();
    }
    
    /**
     * function render
     */
    public function render($view, $type, $component)
    {
        
    }
    
    /**
     * function setContainer
     */
    public function setContainer(Container $container)
    {
        $this->container = $container->createChild();
    }
    
    /**
     * function getContainer
     */
    public function getContainer()
    {
        return $this->container;
    }
    
    /**
     * Method to check whether an ID is in the edit list.
     *
     * @param   string   $context  The context for the session storage.
     * @param   integer  $id       The ID of the record to add to the edit list.
     *
     * @return  boolean  True if the ID is in the edit list.
     *
     * @since   1.0
     */
    protected function checkEditId($context, $id)
    {
        if ($id)
        {
            $app    = $this->getApplication();
            $values = (array) $app->getUserState($context . '.id');

            $result = in_array((int) $id, $values);

            if (defined('JDEBUG') && JDEBUG)
            {
                Log::add(
                    sprintf(
                        'Checking edit ID %s.%s: %d %s',
                        $context,
                        $id,
                        (int) $result,
                        str_replace("\n", ' ', print_r($values, 1))
                    ),
                    Log::INFO,
                    'controller'
                );
            }

            return $result;
        }
        else
        {
            // No id for a new item.
            return true;
        }
    }
    
    /**
     * Method to add a record ID to the edit list.
     *
     * @param   string   $context  The context for the session storage.
     * @param   integer  $id       The ID of the record to add to the edit list.
     *
     * @return  void
     *
     * @since   1.0
     */
    protected function holdEditId($context, $id)
    {
        $app = $this->getApplication();
        $values = (array) $app->getUserState($context . '.id');

        // Add the id to the list if non-zero.
        if (!empty($id))
        {
            array_push($values, (int) $id);
            $values = array_unique($values);
            $app->setUserState($context . '.id', $values);

            if (defined('JDEBUG') && JDEBUG)
            {
                Log::add(
                    sprintf(
                        'Holding edit ID %s.%s %s',
                        $context,
                        $id,
                        str_replace("\n", ' ', print_r($values, 1))
                    ),
                    Log::INFO,
                    'controller'
                );
            }
        }
    }
    
    /**
     * function getDefaultView
     */
    public function getDefaultView()
    {   
        return $this->defaultView ?: $this->getName();
    }
    
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
    public function getView($name = '', $type = '', $nameSpace = '', $config = array())
    {
        // Set default options
        $name       = $name         ?: $this->getDefaultView();
        $nameSpace  = $nameSpace    ?: $this->getNamespace();
        $type       = $type         ?: 'Html';
        
        $nameSpace  = explode('Controller', $nameSpace);
        $nameSpace  = trim( array_shift($nameSpace), '\\');
        
        // Clean the view name
        $viewName        = preg_replace('/[^A-Z0-9_]/i', '', $name);
        $viewType        = preg_replace('/[^A-Z0-9_]/i', '', $type);
 
        // Build the view class name
        $viewClass = ucfirst($nameSpace) . '\\View\\' .
                     ucfirst($viewName) . '\\' . ucfirst($viewName) . ucfirst($viewType) . 'View'
                     ;
         
        if (!class_exists($viewClass))
        {
            throw new \RuntimeException('View Class ' . $viewClass . ' Not found.');
        
            return false;
        }
        
        // Get View from container
        $container = $this->getContainer();
        
        try
        {
            $view = $container->get($viewClass);
        }
        catch(\InvalidArgumentException $e)
        {
            // If View not exists, create one.
            $view = $container->buildObject($viewClass, true);
        }
        
        if (!$view)
        {
            throw new \RuntimeException('View Class ' . $viewClass . ' Not found.');

            return false;
        }
 
        return $view;
    }
    
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
    public function getModel($name = '', $nameSpace = '', $config = array())
    {
        $view  = $this->getView($name, $nameSpace);
        
        $model = $view->getModel();
        
        return $model;
    }
    
    /**
     * Method to get the controller name
     *
     * @return  string  The name of the dispatcher
     *
     * @since   1.0
     */
    public function getName()
    {
        if(!empty($this->name))
        {
            return $this->name;
        }
        
        $name = $this->getReflection()->getShortName();
        $name = substr($name, 0, -10);
        
        return $this->name = $name;
    }
    
    /**
     * Method to get the controller namespace
     *
     * @return  string  The namespace of component
     *
     * @since   1.0
     */
    public function getNamespace()
    {
        if (!empty($this->nameSpace))
        {
            return $this->nameSpace;
        }
 
        return $this->nameSpace = $this->getReflection()->getNamespaceName();
    }
    
    /**
     * function getReflection
     */
    public function getReflection()
    {
        if(empty($this->reflection))
        {
            $this->reflection = new \ReflectionClass($this);
        }
        
        return $this->reflection;
    }
}
