<?php

namespace Jakim\Solat;

use Exception;
use GuzzleHttp\Client as HttpClient;
use Goutte\Client;

class Zon {

	const url = 'http://www.e-solat.gov.my/web/';

	protected $zone_code;

	public function __construct($zone_code)
	{
		if(!is_string($zone_code))
			throw new Exception;

		$this->zone_code = $zone_code;
	}

	public function __toString()
	{
		return $this->zone_code;
	}

	public function getNegeri()
	{
		return '';
	}

	public function getDescription()
	{
		return '';
	}

	public function isValid()
	{
		return true;
	}

	public static function getAllNegeri()
	{
		$client = new Client;
		$crawler = $client->request('GET', static::url .'waktusolat.php');

		$button = $crawler->selectButton('Cari');
		$form = $button->form();

		return array_slice($form['negeri']->availableOptionValues(), 1);
	}

	public static function getAllZon()
	{
		$zones = array();
		foreach(static::getAllNegeri() as $negeriString)
		{
			$zonesNegeri = static::getZon($negeriString);
			if($zones)
				$zones = array_merge($zonesNegeri, $zones);
			else
				$zones = $zonesNegeri;
		}
		return $zones;
	} 

	public static function getZon($negeriString)
	{
		$client = new HttpClient;
		$response = $client->get(static::url .'getzone.php?negeri='.$negeriString);

		$js_script = trim((string) $response->getBody());

		$arr_data = json_decode('['.str_replace('\'', '"', trim(str_replace(array('<script type="text/javascript">', "\n", 'window.parent.handleResponse', '(', ')', ';', '</script>'), '', $js_script))).']', true);
		
		if(!$arr_data[0])
			throw new Exception($negeriString);

		$codes = explode('=', $arr_data[0]);
		$places = explode('=', $arr_data[1]);

		return array_map(function($v, $k) use($places, $negeriString) {
			return [
				'kod' => $v, 
				'tempat' => str_replace('\s\s', '\s', trim($places[$k])),
				'negeri' => $negeriString];
		}, $codes, array_keys($codes));
	}
}