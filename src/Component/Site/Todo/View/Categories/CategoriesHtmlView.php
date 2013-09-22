<?php
/**
 * Part of the Joomla Todo's Categories
 *
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Component\Todo\View\Categories;

use App\Joomla\View\HtmlView;
use App\Joomla\View\Renderer\TwigRenderer;
use App\Joomla\View\Layout\Layout;

use Component\Todo\Model\CategoriesModel;
 
class CategoriesHtmlView extends HtmlView
{
    /**
     * function __construct
     */
    public function __construct(CategoriesModel $model, TwigRenderer $renderer)
    {
        parent::__construct($model, $renderer);
    }
    
    /**
     * function render
     */
    public function render()
    {
        $categories = $this->model->getCategories();
        
        $this->set('categories', $categories);
        
        return parent::render();
    }
}