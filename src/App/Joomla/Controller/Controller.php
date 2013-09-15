<?php
/**
 * Part of the Joomla Tracker Controller Package
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
//use JTracker\Application\TrackerApplication;
//use JTracker\View\AbstractTrackerHtmlView;

/**
 * Abstract Controller class for the Tracker Application
 *
 * @since  1.0
 */
abstract class Controller extends AbstractController
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
        $vFormat = $input->getWord('format', 'html');
        $lName   = $input->getCmd('layout', $this->getDefaultView().'/index');

        $input->set('view', $vName);

        $base   = $this->nameSpace . ucfirst($this->component);
        //$mClass = $base . '\\Model\\' . ucfirst($vName) . 'Model';
        //
        //// If a model doesn't exist for our view, revert to the default model
        //if (!class_exists($mClass))
        //{
        //    $mClass = $base . '\\Model\\DefaultModel';
        //
        //    // If there still isn't a class, panic.
        //    if (!class_exists($mClass))
        //    {
        //        throw new \RuntimeException(sprintf('No model found for view %s or a default model for %s', $vName, $this->component));
        //    }
        //}

        
        $view = $this->getView($vName, $vFormat);
        //$view->setLayout($vName . '.' . $lName);
        
        //$model = $this->getModel($vName);
        
        //try
        //{
            // Render our view.
            echo $view->render();
        //}
        //catch (\Exception $e)
        //{
        //    echo $this->getApplication()->getDebugger()->renderException($e);
        //}

        return;
        
    }
    
    /**
     * function render
     */
    public function render($view, $type, $component)
    {
        
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
        if($this->defaultView)
        {
            return $this->defaultView ;
        }
        
        return $this->defaultView = $this->getName();
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
        static $views;
 
        if (!isset($views))
        {
            $views = array();
        }
 
        if (empty($name))
        {
            $name = $this->getName();
        }
 
        if (empty($nameSpace))
        {
            $nameSpace = $this->getNamespace();
            $nameSpace = substr($nameSpace, 0, -11);
        }
        
        if (empty($type))
        {
            $type = 'Html';
        }
 
        if (empty($views[$name]))
        {
            if ($view = $this->createView($name, $nameSpace, $type, $config))
            {
                $views[$name] = & $view;
            }
            else
            {
                throw new \RuntimeException('View: ' . $view . $type . ' not found.');
 
                return $result;
            }
        }
 
        return $views[$name];
    }
    
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
    protected function createView($name, $nameSpace = '', $type = '', $config = array())
    {
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
        }
        
        return new $viewClass($this->getModel($viewName));
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
        if (empty($name))
        {
            $name = $this->getName();
        }
 
        if (empty($nameSpace))
        {
            $nameSpace = $this->getNamespace();
            $nameSpace = substr($nameSpace, 0, -11);
        }
 
        if (!$model = $this->createModel($name, $nameSpace, $config))
        {
            return false;
        }
        return $model;
    }
    
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
    protected function createModel($name, $nameSpace = '', $config = array())
    {
        // Clean the model name
        $modelName   = preg_replace('/[^A-Z0-9_]/i', '', $name);
 
        // Build the model class name
        $modelClass = ucfirst($nameSpace) . '\\Model\\' . ucfirst($modelName) . 'Model';
 
        if (!class_exists($modelClass))
        {
            throw new \RuntimeException('Model Class ' . $modelClass . ' Not found.');
        }
 
        return new $modelClass();
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
