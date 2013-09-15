<?php
/**
 * Part of the Joomla Tracker View Package
 *
 * @copyright  Copyright (C) 2012 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace App\Joomla\View;

use App\Joomla\View\Renderer\RendererInterface;

interface ViewInterface
{
    public function setRenderer(RendererInterface $renderer);
    
    public function getRenderer();
}


