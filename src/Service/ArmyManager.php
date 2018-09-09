<?php

namespace App\Service;

class ArmyManager
{
    /** @var UnitManager */
    private $unitManager;

    public function __construct(UnitManager $unitManager)
    {
        $this->unitManager = $unitManager;
    }

    public function getRandomUnit()
    {
        $units = $this->unitManager->getUnits();

        return $units[array_rand($units)];
    }

    public function generateArmy(int $armySize = 5)
    {
        $army = [];
        while (count($army) < $armySize) {
            $army[] = clone $this->getRandomUnit();
        }

        return $army;
    }
}