<?php

namespace Jakim\Solat;

use GuzzleHttp\Client as HttpClient;
use Goutte\Client;
use Jakim\Solat\Zon;
use Jakim\Solat\Exceptions\InfoNotFoundException;
use Purl\Url;

class Waktu {

	protected $zon;
	
	public function __construct(Zon $zon)
	{
		$this->zon = $zon;
	}

	public function getToday()
	{
		$client = new HttpClient;
		$response = $client->get('http://www2.e-solat.gov.my/xml/today/?zon='.$this->zon);
		$xml = $response->xml();

		if(!isset($xml->channel) || $xml->channel->link == "")
			throw new InfoNotFoundException;

		$prayer_time = array(
				'date' => date('d F Y'),
				'day' => date('l')
			);
		
		foreach($xml->channel->item as $item)
		{
			$prayer_time[strtolower((string) $item->title)] = (string) $item->description;
		}

		return array(
				'zone_code' => (string) $this->zon,
				'zone_description' => strtoupper((string) $xml->channel->link),
				'prayer_times' => array($prayer_time)
			);
	}

	public function getWeekly()
	{
		$url = $this->getTableUrl();
		$url->query->setData(array(
			'zone' => (string) $this->zon,
    		'jenis' => 'week'
		));

		return $this->getTableData($url);
	}

	public function getMonthly($month = 0, $year = 0)
	{
		$query = array(
			'zone' => (string) $this->zon,
    		'jenis' => 'year',
    		'year' => $year ?: date('Y')
		);

		if($month)
			$query['bulan'] = str_pad($month, 2, '0', STR_PAD_LEFT);

		$url = $this->getTableUrl();
		$url->query->setData($query);

		return $this->getTableData($url);
	}

	protected function getTableUrl()
	{
		return new Url('http://www.e-solat.gov.my/web/muatturun.php');
	}

	protected function getTableData(Url $tableUrl)
	{
		$client = new Client;
		$crawler = $client->request('GET', (string) $tableUrl);

		$tableData = $crawler->filter('table')->eq(1)->filter('tr')->each(function($node, $i) {
			return $node->filter('td')->each(function($node, $i) {
				return trim(str_replace('\n','',$node->text()));
			});
		});

		if(count($tableData) < 1)
			throw new InfoNotFoundException;
			
		return array(
			'zone_code' => (string) $this->zon,
			'zone_description' => trim($crawler->filter('table')->eq(0)->text()),
			'prayer_times' => array_map(function($data) {
				return array(
					'date' => $data[0],
					'day' => $data[1],
					'imsak' => $data[2],
					'subuh' => $data[3],
					'syuruk' => $data[4],
					'zuhur' => $data[5],
					'asar' => $data[6],
					'maghrib' => $data[7],
					'isyak' => $data[8]
				);
			}, array_slice($tableData, 1))
		);
	}
}