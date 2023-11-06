<?php

namespace app\models;

class Currency {
    public string $code;
    public string $name;
    public int|float $weight;

    public function __construct(string $code, string $name, int|float $weight) 
    {
        $this->code = $code;
        $this->name = $name;
        $this->weight = $weight;
    }

    private static $currencies = array();

    public static function getCurrencies(): array {
        if(!self::$currencies) {
            self::$currencies = [
                "PL" => new Currency("PLN","Polish Zloty",1),
                "DE" => new Currency("EUR","Euro",0.2244),
                "GB" => new Currency("GBP","British Pound Sterling",0.1945),
            ];
        }
        return self::$currencies;
    }

    public static function findOneByCountryCode($countryCode): ?Currency
    {
        try {
            return self::getCurrencies()[$countryCode];
        } catch(\Exception $exception) {
            throw $exception;
        }
    }
}