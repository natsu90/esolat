## E-Solat JAKIM Library Wrapper

## Install

```sh
composer require jakim/esolat
```

## Usage

```php
use Jakim\Solat\Zon;
use Jakim\Solat\Waktu;

$zones = Zon::getAllZone();

$zon = new Zon('sgr01');
$waktu = new Waktu($zon);

// get today waktu
$waktu->getToday();

// get weekly waktu
$waktu->getWeekly();

// get current month waktu
$waktu->getMonthly();

// get monthly waktu on October 2013
$waktu->getMonthly(10, 2013);
```

## Todo
* Add more tests
