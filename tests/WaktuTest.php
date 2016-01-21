<?php

namespace Jakim\Solat\Tests;

use Jakim\Solat\Zon;
use Jakim\Solat\Waktu;

class WaktuTest extends \PHPUnit_Framework_TestCase {

	public function dataWaktu()
	{
		return array(
			array('sgr01'),
			array('SGR02'),
			array('sgr03', 12, 2015),
			array('sgr04', 1)
			);
	}

	/**
	 * @dataProvider dataWaktu
	 */

	public function testWaktu($zon_code, $month = 0, $year = 0)
	{
		$zon = new Zon($zon_code);
		$waktu = new Waktu($zon);
		var_dump($waktu->getToday());
		var_dump($waktu->getWeekly());
		var_dump($waktu->getMonthly($month, $year));
	}	
}