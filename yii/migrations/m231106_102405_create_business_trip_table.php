<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%business_trip}}`.
 */
class m231106_102405_create_business_trip_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%business_trip}}', [
            'id' => $this->primaryKey(),
            'employeeId' => $this->integer(),
            'start' => $this->dateTime(),
            'end' => $this->dateTime(),
            'countryCode' => $this->string(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%business_trip}}');
    }
}
