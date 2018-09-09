<?php

namespace App\Entity;

class Armor extends BaseEntity
{
    const TYPE_LIGHT = 'light';
    const TYPE_MEDIUM = 'medium';
    const TYPE_HEAVY = 'heavy';
    const TYPE_FORTIFIED = 'fortified';

    protected $type;

    protected $value;

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}