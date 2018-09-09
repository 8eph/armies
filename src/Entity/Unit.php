<?php

namespace App\Entity;

class Unit extends BaseEntity
{
    protected $hitpoints;

    protected $armor;

    protected $weapon;

    /** @var bool */
    private $attackedThisRound;

    /**
     * @return int
     */
    public function getHitpoints()
    {
        return $this->hitpoints;
    }

    public function takeDamage(int $damage)
    {
        $this->hitpoints -= $damage;
    }

    /**
     * @return Weapon
     */
    public function getWeapon()
    {
        return $this->weapon;
    }

    /**
     * @return Armor
     */
    public function getArmor()
    {
        return $this->armor;
    }

    public function isDead()
    {
        return $this->getHitpoints() < 1;
    }

    public function setAttackedThisRound(bool $attackedThisRound)
    {
        $this->attackedThisRound = $attackedThisRound;
    }

    public function hasAttackedThisRound()
    {
        return $this->attackedThisRound;
    }
}