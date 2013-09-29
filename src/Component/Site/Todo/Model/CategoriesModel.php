<?php
/**
 * Part of the Joomla Todo's Categories
 *
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Component\Site\Todo\Model;

use App\Joomla\Model\Model;

use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
//use App\Joomla\View\HtmlView;
 
class CategoriesModel extends Model
{
    /**
     * function getCtegories
     */
    public function getCategories()
    {
        $paths = array(__DIR__.'/Entity');
        $isDevMode = false;
        
        // the connection configuration
        $dbParams = array(
            'driver'   => 'pdo_mysql',
            'user'     => 'root',
            'password' => '1234',
            'dbname'   => 'jtodo',
        );
        
        $config = Setup::createAnnotationMetadataConfiguration($paths, $isDevMode);
        $entityManager = EntityManager::create($dbParams, $config);
        
        return array(
            array(
                'id' => 1,
                'title' => 'Asika'
            ),
            array(
                'id' => 2,
                'title' => 'Bryan'
            ),
            array(
                'id' => 3,
                'title' => 'Jordan'
            )
        );
    }
}