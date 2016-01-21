# E-Solat JAKIM Library Wrapper

## Install

Run `composer require jakim/esolat`

## Usage

    $zones = Zon::getAllZone();

    $zon = new Jakim\Solat\Zon('sgr01');
    $waktu = new Jakim\Solat\Waktu($zon);

    // get today waktu
    $waktu->getToday();

    // get weekly waktu
    $waktu->getWeekly();

    // get whole current month waktu
    $waktu->getMonthly();

    // get whole month waktu on October 2013
    $waktu->getMonthly(10, 2013);

## Todo
* Add more tests
