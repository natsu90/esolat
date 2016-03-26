<?php

namespace Jakim\Solat\Tests;

use Jakim\Solat\Zon;

class ZonTest extends \PHPUnit_Framework_TestCase {

	public function testZon()
	{
		var_dump(Zon::getAllNegeri());
		$zones = Zon::getAllZon();
		//var_dump(Zon::getZon('Brunei'));
		$zon = new Zon('sgr01');
		var_dump($zon->getDescription());
		var_dump($zon->getNegeri());
	}
}