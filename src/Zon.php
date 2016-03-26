<?php

namespace Jakim\Solat;

use Exception;
use GuzzleHttp\Client as HttpClient;
use Goutte\Client;
use Desarrolla2\Cache\Cache;
use Desarrolla2\Cache\Adapter\File;
use Jakim\Solat\Exceptions\NegeriNotFoundException;
use Jakim\Solat\Exceptions\ZoneCodeNotFoundException;

class Zon {

	const url = 'http://www.e-solat.gov.my/web/';

	protected $zone_code;

	public function __construct($zone_code)
	{
		if(!self::isCodeValid($zone_code))
			throw new ZoneCodeNotFoundException;

		$this->zone_code = $zone_code;
	}

	public function __toString()
	{
		return $this->zone_code;
	}

	public function getNegeri()
	{
		$zon = self::getZonData($this->zone_code);
		return $zon['negeri'];
	}

	public function getDescription()
	{
		$zon = self::getZonData($this->zone_code);
		return $zon['zone_description'];
	}

	public static function getZonData($zone_code)
	{
		$zones = self::getAllZon();

		foreach($zones as $zone)
		{
			if(strtolower($zone_code) == strtolower($zone['zone_code']))
				return $zone;
		}

		return false;
	}

	public static function isCodeValid($zone_code)
	{
		return (bool) self::getZonData($zone_code);
	}

	public static function getAllNegeri()
	{
		$client = new Client;
		$crawler = $client->request('GET', self::url .'waktusolat.php');

		$button = $crawler->selectButton('Cari');
		$form = $button->form();

		return array_slice($form['negeri']->availableOptionValues(), 1);
	}

	public static function getAllZon()
	{
		$zones = array();
		foreach(self::getAllNegeri() as $negeri_string)
		{
			$zones_negeri = self::getZon($negeri_string);
			if($zones)
				$zones = array_merge($zones_negeri, $zones);
			else
				$zones = $zones_negeri;
		}
		return $zones;
	} 

	public static function getZon($negeri_string)
	{
		$cache_key = 'zon_'.$negeri_string;
		$cache_dir = 'cache';
		$adapter = new File($cache_dir);
		$cache = new Cache($adapter);

		if($cache->has($cache_key))
			return $cache->get($cache_key);

		$client = new HttpClient;
		$response = $client->get(self::url .'getzone.php?negeri='.$negeri_string);

		$js_script = trim((string) $response->getBody());

		$arr_data = json_decode('['.str_replace('\'', '"', trim(str_replace(array('<script type="text/javascript">', "\n", 'window.parent.handleResponse', '(', ')', ';', '</script>'), '', $js_script))).']', true);
		
		if(!$arr_data[0])
			throw new Exception($negeri_string .' negeri not recognized');

		$codes = explode('=', $arr_data[0]);
		$places = explode('=', $arr_data[1]);

		$data = array_map(function($v, $k) use($places, $negeri_string) {
			return [
				'zone_code' => $v, 
				'zone_description' => str_replace('\s\s', '\s', trim($places[$k])),
				'negeri' => $negeri_string];
		}, $codes, array_keys($codes));

		$cache->set($cache_key, $data);
		return $data;
	}
}