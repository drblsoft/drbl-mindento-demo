<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * BusinessTrip
 * 
 * @property int $id
 * @property int $employeeId
 * @property \DateTime $start
 * @property \DateTime $end
 * @property string $countryCode
 */
class BusinessTrip extends ActiveRecord
{
}