<?php

namespace app\services;

use app\models\BusinessTrip;
use app\models\BusinessTripDiet;
use app\models\Employee;
use app\models\Country;
use app\models\Currency;
use app\services\BusinessTripDietPricing;

class BusinessTripManagerService
{
    private BusinessTripDietPricing $pricing;

    public function __construct(BusinessTripDietPricing $pricing) {
        $this->pricing = $pricing;
    }

    public function buildBusinessTrip(int $employeeId, string $start, string $end, string $countryCode): BusinessTrip {
        $employee = Employee::findOne($employeeId);
        if(!$employee) {
            throw new \Exception("Employee(".$employeeId.") not found");
        }
        $country = Country::findOneByCountryCode($countryCode);
        if(!$country) {
            throw new \Exception("Country(".$countryCode.") not found");
        }
        if(new \DateTime($start) > new \DateTime($end)) {
            throw new \Exception("Start cannot be greater than End");
        }
        $businessTrips = BusinessTrip::find()
            ->where(["employeeId"=>$employee->id])
            ->all();
        $startDT = new \DateTime($start);
        $endDT = new \DateTime($end);
        $isDTBetween = false;
        foreach($businessTrips as $businessTrip) {
            $btStartDT = new \DateTime($businessTrip->start);
            $btEndDT = new \DateTime($businessTrip->end);
            $isDTBetween = $isDTBetween || (
                $startDT >= $btStartDT && $startDT <= $btEndDT
            ) || (
                $endDT >= $btStartDT && $endDT <= $btEndDT
            );
        }
        if($isDTBetween) {
            throw new \Exception("This period is not available for Employee(".$employeeId.")");
        }
        $businessTrip = new BusinessTrip();
        $businessTrip->employeeId = $employeeId;
        $businessTrip->start = $start;
        $businessTrip->end = $end;
        $businessTrip->countryCode = $countryCode;
        return $businessTrip;
    }

    public function calculateBusinessTripsDietsForEmployee(int $employeeId): array
    {
        $result = array();
        $employee = Employee::findOne($employeeId);
        $businessTrips = BusinessTrip::find()->where(["employeeId"=> $employee->id])->all();
        foreach($businessTrips as $businessTrip) {
            $result[] = $this->calculateBusinessTripDiet($businessTrip);
        }
        return $result;
    }

    private function calculateBusinessTripDiet(BusinessTrip $businessTrip): BusinessTripDiet
    {
        $businessTripDiet = new BusinessTripDiet(
            $businessTrip->start,
            $businessTrip->end,
            $businessTrip->countryCode,
        );
        $currency = Currency::findOneByCountryCode($businessTrip->countryCode);
        $businessTripDiet->currency = $currency;
        $businessTripDiet->amountDue = $this->pricing->estimate(
            $businessTrip->start,
            $businessTrip->end,
            $businessTrip->countryCode
        ) * $currency->weight;
        return $businessTripDiet;
    }
}