<?php

namespace App\Joomla\Controller\Tests\Stubs;

use App\Joomla\Controller\Controller;

class BaseController extends Controller
{
    public function execute()
    {
        return 'base';
    }
}