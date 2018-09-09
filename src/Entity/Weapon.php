<?php

namespace App\Entity;

class Weapon extends BaseEntity
{
    const TYPE_LIGHT = 'light';
    const TYPE_MEDIUM = 'medium';
    const TYPE_HEAVY = 'heavy';
    const TYPE_FORTIFIED = 'fortified';

    /** @var */
    protected $speed;

    /** @var int */
    protected $damage;

    /** @var int */
    protected $type;

    /**
     * @return mixed
     */
    public function getAttackSpeed()
    {
        return $this->speed;
    }

    /**
     * @return mixed
     */
    public function getDamage()
    {
        return $this->damage;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }
}