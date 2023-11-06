<?php

namespace app\models;

class BusinessTripDiet 
{
    public string $start;
    public string $end;
    public Country $country;
    public int|float $amountDue;
    public Currency $currency;

    public function __construct(string $start, string $end, string $countryCode) {
        $this->start = $start;
        $this->end = $end;
        $this->country = Country::findOneByCountryCode($countryCode);
    }
}