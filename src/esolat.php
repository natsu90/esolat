<?php

class Esolat {
	
	public function getWaktuSolat($zoneCode, $timeString = 'today')
	{
		$zoneCode = strtoupper($zoneCode);

		if(!$this->isZoneCodeValid($zoneCode))
			throw new \Exception('Invalid Zone Code');
			
		if(strtolower($timeString) == 'today') {

			$client = new GuzzleHttp\Client();
			$response = $client->get('http://www2.e-solat.gov.my/xml/today/?zon='.$zoneCode);
			$xml = $response->xml();

			if(!isset($xml->channel) || $xml->channel->link == "")
				throw new \Exception('Error from JAKIM');

			$prayer_time = array(
				'date' => date('d F Y'),
				'day' => date('l')
			);
			foreach($xml->channel->item as $item)
			{
				$prayer_time[strtolower((string) $item->title)] = (string) $item->description;
			}

			return array(
				'zone_code' => $zoneCode,
				'zone_description' => strtoupper((string) $xml->channel->link),
				'prayer_times' => array($prayer_time)
			);

		}

		$year = date('Y');
		$esolatLink = 'http://www.e-solat.gov.my/web/muatturun.php?zone='.$zoneCode;

		switch(strtolower($timeString)) 
		{
			case 'week':
					$esolatLink .= '&jenis=week';
					break;
				case 'month':
					$esolatLink .= '&jenis=year&year='.$year;
					break;
				default:
					if(strpos($timeString, '-') === false)
						throw new \Exception('Invalid $timeString value, only accepting following values; today/week/month or {month}-{year} e.g 01-2003, 12-'.$year);
					$timeParam = explode('-', $timeString, 2);
					if((int) $timeParam[0] < 1 || (int) $timeParam[0] > 12)
						throw new \Exception('Invalid month value, only accepting values between 01 to 12');
					if((int) $timeParam[1] < 2003 || (int) $timeParam[1] > $year)
						throw new \Exception('Invalid year value, only accepting values between 2003 to '.$year);
					$year = $timeParam[1];
					$esolatLink .= '&jenis=year&year='.$year.'&bulan='.str_pad($timeParam[0],2,'0',STR_PAD_LEFT);
			}

			$client = new Goutte\Client();
			$crawler = $client->request('GET', $esolatLink);

			$tableData = $crawler->filter('table')->eq(1)->filter('tr')->each(function($node, $i) {
				return $node->filter('td')->each(function($node, $i) {
					return trim(str_replace('\n','',$node->text()));
				});
			});

			if(count($tableData) < 1)
				throw new \Exception('No table result found');
				
			return array(
				'zone_code' => $zoneCode,
				'zone_description' => trim(str_replace(array($year, '\n'), '', $crawler->filter('table')->eq(0)->text())),
				'prayer_times' =>
					array_map(function($data) use($year) {
						return array(
							'date' => $data[0].' '.$year,
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

	public function getZoneCodes($stateCode = '')
	{
		$zoneCodeData = json_decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . '..'. DIRECTORY_SEPARATOR .'solat_zone.json'), true);
		$stateCode = strtoupper($stateCode);

		if($stateCode)
			$zoneCodeData = array_filter($zoneCodeData, function($data) use($stateCode) {
				return $data['code'] == $stateCode;
			});

		if(!$zoneCodeData)
			throw new Exception('Invalid State Code');

		return array_values($zoneCodeData);
	}

	public function isZoneCodeValid($zoneCode)
	{
		$zoneCodeParam = preg_split('#(?<=[a-zA-Z])(?=\d)#i', $zoneCode);
		$zoneCodeData = $this->getZoneCodes($zoneCodeParam[0]);

		foreach($zoneCodeData[0]['zone'] as $i => $zoneData)
		{
			if($zoneData['code'] == str_pad($zoneCodeParam[1],2,'0',STR_PAD_LEFT))
				return true;
		}

		return false;
	}

	public function getSolatZoneCodes($stateCode = '')
	{
		$zoneCodeData = $this->getZoneCodes($stateCode);
		
		$zoneCodes = array();
		foreach($zoneCodeData as $i => $stateData)
		{
			foreach($stateData['zone'] as $i => $zoneData)
			{
				$zoneCodes[] = array(
					'code' => $stateData['code'].$zoneData['code'],
					'name' => $zoneData['name']
				);
			}
		}

		return $zoneCodes;
	}
}