<?php

namespace app\services;

use app\models\ValueInRange;

class BusinessTripDietPricing 
{
    private static $paidWeekdays = ["1","2","3","4","5"];
    private function isPaidWeekday(string $weekday): bool
    {
        return in_array($weekday, self::$paidWeekdays);
    }

    private static $dailyRates = array();
    private function getDailyRate(int $workingDaysCount): int|float 
    {
        if(!self::$dailyRates) {
            self::$dailyRates = [
                0 => new ValueInRange(-INF,0,0),
                1 => new ValueInRange(1,7,1),
                2 => new ValueInRange(8,INF,2),
            ];
        }
        foreach(self::$dailyRates as $valueInRange) {
            if($valueInRange->isInRange($workingDaysCount)) {
                return $valueInRange->value;
            }
        }
        throw new \Exception("Wrong daily rate");
    }

    private static $hourlyRates = array();
    private function getHourlyRate(int $workingHoursCount): int|float {
        if(!self::$hourlyRates) {
            self::$hourlyRates = [
                0 => new ValueInRange(-INF,7,0),
                1 => new ValueInRange(8,INF,1),
            ];
        }
        foreach(self::$hourlyRates as $valueInRange) {
            if($valueInRange->isInRange($workingHoursCount)) {
                return $valueInRange->value;
            }
        }
        throw new \Exception("Wrong hourly rate");
    }
    private static $countryRates = array();
    private function getCountryRate(string $countryCode): int|float {
        if(!self::$countryRates) {
            self::$countryRates = [
                "PL" => 10,
                "DE" => 50,
                "GB" => 75,
            ];
        }
        if(isset(self::$countryRates[$countryCode])) {
            return self::$countryRates[$countryCode];
        } else {
            throw new \Exception("Wrong country code");
        }
    }

    public function estimate(string $start, string $end, string $countryCode): int|float {
        $result = 0;
        $startDT = new \DateTime($start);
        $endDT = new \DateTime($end);
        $interval = \DateInterval::createFromDateString('1 hour');
        $period = new \DatePeriod($startDT, $interval, $endDT);
        $workingHour = 0;
        $workingDaysCount = 0;
        $previousWeekday = null;
        $paidDays = [];
        foreach ($period as $hour) {
            $weekday = date('w', ($hour)->getTimestamp());
            $isNewWeekday = $previousWeekday === null || $previousWeekday != $weekday;
            $workingHour = $isNewWeekday ? 0 : $workingHour + 1;
            $workingDaysCount = $isNewWeekday? $workingDaysCount + 1 : $workingDaysCount;
            if($this->isPaidWeekday($weekday)) {
                $countryRate = $this->getCountryRate($countryCode);
                $hourlyRate = $this->getHourlyRate($workingHour);
                $dailyRate = $this->getDailyRate($workingDaysCount);
                $diet = $countryRate * $hourlyRate * $dailyRate;
                $paidDays[$workingDaysCount] = $diet;
            }
            $previousWeekday = $weekday;
        }
        return array_sum($paidDays);
    }
}