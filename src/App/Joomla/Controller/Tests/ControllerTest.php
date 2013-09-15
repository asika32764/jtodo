<?php

namespace App\Joomla\Tests\Controller;

use App\Joomla\Controller\Controller;
use App\Joomla\Controller\Tests\Stubs\BaseController;

class ControllerTest extends \PHPUnit_Framework_TestCase
{
    /**
	 * @var Controller
	 * @since  1.0
	 */
	protected $instance;

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @since  1.0
	 * @return void
	 */
	protected function setUp()
	{
		parent::setUp();
		
		$this->instance = new BaseController;
	}
	
	/**
	 * function testGetName
	 */
	public function testGetName()
	{
		$name = $this->instance->getName();
		$this->assertSame($name, 'Base', 'getName() not equal to Controller Name.');
	}
}