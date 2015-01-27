# E-Solat JAKIM API Wrapper

##Demo
[http://esolatapi-namialus.rhcloud.com/](http://esolatapi-namialus.rhcloud.com/)

## Install

Add following values to `composer.json`

    ...
    "repositories": [
    	...
    	{
    		"type": "git",
        	"url": "https://github.com/natsu90/esolat"
        },
        ...
    ],
    ...
    "require": {
        ...
        "natsu90/esolat": "dev-master",
        ...
    }
    ...

then run `composer update natsu90/esolat`

## Usage

    $esolat = new Esolat();

    // get today waktu
    print_r($esolat->getWaktuSolat('SGR01','today'));

    // get a week waktu
    print_r($esolat->getWaktuSolat('SGR01','week'));

    // get whole current month waktu
    print_r($esolat->getWaktuSolat('SGR01','month'));

    // get whole month waktu on October 2013
    print_r($esolat->getWaktuSolat('SGR01','10-2013'));

    // get all available solat zone code
    print_r($esolat->getSolatZoneCodes());