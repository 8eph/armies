<?php

namespace App\Service;

use App\Entity\Armor;
use App\Entity\Unit;
use App\Entity\Weapon;
use Symfony\Component\Yaml\Yaml;

class UnitManager
{
    /** @var array */
    private $config;
    /** @var Weapon[] */
    private $weapons;
    /** @var Unit[] */
    private $units;
    /** @var Armor[] */
    private $armor;

    public function __construct()
    {
        $this->init();
    }

    private function init()
    {
        $this->config = Yaml::parseFile(__DIR__.'/../Configuration/config.yml');
        $this->weapons = [];
        $this->armor = [];
        $this->units = [];

        foreach($this->config['weapons'] as $weaponConfig) {
            $this->weapons[$weaponConfig['name']] = new Weapon($weaponConfig);
        }
        foreach($this->config['armor'] as $armorConfig) {
            $this->armor[$armorConfig['name']] = new Armor($armorConfig);
        }
        foreach($this->config['units'] as $unitConfig) {
            $unitConfig['weapon'] = $this->weapons[$unitConfig['weapon']];
            $unitConfig['armor'] = $this->armor[$unitConfig['armor']];
            $this->units[$unitConfig['name']] = new Unit($unitConfig);
        }
    }

    public function getUnits()
    {
        return $this->units;
    }
}