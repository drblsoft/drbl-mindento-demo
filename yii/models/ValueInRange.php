<?php

namespace app\models;

class ValueInRange
{
    public int|float $from;
    public int|float $to;
    public int|float $value;

    public function __construct(int|float $from, int|float $to, int|float $value) {
        $this->from = $from;
        $this->to = $to;
        $this->value = $value;
    }

    public function isInRange(int|float $value): bool {
        return $value >= $this->from && $value <= $this->to;
    }
}