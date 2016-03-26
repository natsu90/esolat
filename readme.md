## E-Solat JAKIM Library Wrapper

## Install

```sh
composer require jakim/esolat
```

## Usage

```php
use Jakim\Solat\Zon;
use Jakim\Solat\Waktu;

$zones = Zon::getAllZone(); // return array

$zon = new Zon('sgr01');
echo $zon->getDescription(); // Gombak,H.Selangor,Rawang, H.Langat,Sepang,Petaling,  S.Alam
echo $zon->getNegeri(); // SELANGOR

$waktu = new Waktu($zon);

// get today waktu
$waktu->getToday(); // return array

// get weekly waktu
$waktu->getWeekly(); // return array

// get current month waktu
$waktu->getMonthly(); // return array

// get monthly waktu on October 2013
$waktu->getMonthly(10, 2013); // return array
```

## Todo
* Add more tests
