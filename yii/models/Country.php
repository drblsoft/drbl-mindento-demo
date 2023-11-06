<?php

namespace app\models;

class Country 
{
    public string $code;
    public string $name;
    public string $currencyCode;

    public function __construct(string $code, string $name, string $currencyCode) 
    {
        $this->code = $code;
        $this->name = $name;
        $this->currencyCode = $currencyCode;
    }

    private static $countries = array();
    public static function getCountries(): array {
        if(!self::$countries) {
            self::$countries = [
                "PL" => new Country("PL","Poland","PLN"),
                "DE" => new Country("DE","Germany","EUR"),
                "GB" => new Country("GB","Great Britain","GBP"),
            ];
        }
        return self::$countries;
    }

    public static function findOneByCountryCode(string $countryCode): ?Country
    {
        try {
            return self::getCountries()[$countryCode];
        } catch(\Exception $exception) {
            throw $exception;
        }
    }
}