<?php

namespace Jakim\Solat\Tests;

use Jakim\Solat\Zon;

class ZonTest extends \PHPUnit_Framework_TestCase {

	public function testZon()
	{
		var_dump(Zon::getAllNegeri());
		var_dump(Zon::getAllZon());
	}
}