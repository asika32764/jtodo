<?php
/**
 * Part of the Joomla Todo's Categories
 *
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Component\Todo\Model;

use App\Joomla\Model\Model;
//use App\Joomla\View\HtmlView;
 
class CategoriesModel extends Model
{
    /**
     * function getCtegories
     */
    public function getCategories()
    {
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