<?php

namespace App\Service;

use App\Entity\Unit;
use App\Event\BattleEvent;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class BattleManager
{
    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var OutputInterface */
    private $output;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function battle(array $army1, array $army2, $maxRounds, OutputInterface $output)
    {
        if (empty($army1) || empty($army2)) {
            throw new \LogicException('The army tries to battle their personal demons as there is no-one else on the battlefield.');
        }

        $this->output = $output;
        $this->eventDispatcher->dispatch(BattleEvent::BATTLE_STARTING, new BattleEvent(['army1' => $army1, 'army2' => $army2, 'output' => $this->output]));
        $round = 0;

        while (++$round < $maxRounds && !empty($army1) && !empty($army2)) {
            [$army1, $army2] = $this->fightRound($army1, $army2, $round);
        }

        $this->eventDispatcher->dispatch(BattleEvent::BATTLE_FINISHED, new BattleEvent(['army1' => $army1, 'army2' => $army2, 'output' => $this->output]));
    }

    public function fightRound(array $army1, array $army2, $roundId)
    {
        $this->resetAttacks($army1, $army2);

        [$largerArmy, $smallerArmy] = $this->sortArmiesBySize($army1, $army2);

        $numberOfAttacks = \count($largerArmy);

        /** @var Unit $army1Unit */
        while (true) {
            if ($numberOfAttacks-- < 1 || empty($army1) || empty($army2)) break;

            $unit1 = array_pop($army1);
            $unit2 = array_pop($army2);

            [$unit1, $unit2] = $this->fightWith($unit1, $unit2);

            if (!$unit1->isDead()) {
                array_unshift($army1, $unit1);
            }

            if (!$unit2->isDead()) {
                array_unshift($army2, $unit2);
            }
        }

        $this->eventDispatcher->dispatch(BattleEvent::ROUND_FINISHED, new BattleEvent([
            'roundId' => $roundId,
            'output' => $this->output
        ]));

        return [$army1, $army2];
    }

    private function getDamage(Unit $damager, Unit $damagee)
    {
        $coefficient = 1;
        switch ($damager->getWeapon()->getType()) {
            case 'medium': $coefficient *= 1.1; breaK;
            case 'heavy': $coefficient *= 1.2; breaK;
            case 'fortified': $coefficient *= 1.3; breaK;
            default: break;
        }
        switch ($damagee->getArmor()->getType()) {
            case 'medium': $coefficient *= 0.9; breaK;
            case 'heavy': $coefficient *= 0.8; breaK;
            case 'fortified': $coefficient *= 0.7; breaK;
            default: break;
        }

        $actualDamage = $coefficient * ($damager->getWeapon()->getDamage() - $damagee->getArmor()->getValue());

        if ($actualDamage < 0) {
            $actualDamage = 0;
        }

        return [
            round($actualDamage * $damager->getWeapon()->getAttackSpeed()),
            round($coefficient * $damager->getWeapon()->getDamage() - $actualDamage)
        ];
    }

    public function fightWith(Unit $unit1, Unit $unit2)
    {
        [$damage1, $mitigatedDamage1] = $this->getDamage($unit2, $unit1);
        [$damage2, $mitigatedDamage2] = $this->getDamage($unit1, $unit2);

        if (!$unit2->hasAttackedThisRound() && !$unit1->isDead()) {
            $this->eventDispatcher->dispatch(BattleEvent::UNIT_DAMAGED, new BattleEvent([
                'damagee' => $unit1,
                'damager' => $unit2,
                'damage' => $damage1,
                'mitigated' => $mitigatedDamage1,
                'output' => $this->output,
                'armyId' => 2,
            ]));
            $unit1->takeDamage($damage1);
            $unit2->setAttackedThisRound(true);
        }

        if (!$unit1->hasAttackedThisRound() && !$unit2->isDead()) {
            $this->eventDispatcher->dispatch(BattleEvent::UNIT_DAMAGED, new BattleEvent([
                'damagee' => $unit2,
                'damager' => $unit1,
                'damage' => $damage2,
                'mitigated' => $mitigatedDamage2,
                'output' => $this->output,
                'armyId' => 1,
            ]));

            $unit2->takeDamage($damage2);
            $unit1->setAttackedThisRound(true);
        }

        if ($unit1->isDead()) {
            $this->eventDispatcher->dispatch(BattleEvent::UNIT_KILLED, new BattleEvent([
                'damagee' => $unit1,
                'damager' => $unit2,
                'armyId' => 1
            ]));
        }

        if ($unit2->isDead()) {
            $this->eventDispatcher->dispatch(BattleEvent::UNIT_KILLED, new BattleEvent([
                'damagee' => $unit2,
                'damager' => $unit1,
                'armyId' => 2
            ]));
        }

        return [$unit1, $unit2];
    }

    private function sortArmiesBySize(array $army1, array $army2)
    {
        // the larger army can do multiple attacks to individual smaller armies' units per round
        if (\count($army1) > \count($army2)) {
            $largerArmy = $army1;
            $smallerArmy = $army2;
        } else {
            $largerArmy = $army2;
            $smallerArmy = $army1;
        }

        return  [$largerArmy, $smallerArmy];
    }

    private function resetAttacks(array $army1, array $army2)
    {
        foreach ($army1 as $unit) {
            $unit->setAttackedThisRound(false);
        }

        foreach ($army2 as $unit) {
            $unit->setAttackedThisRound(false);
        }
    }
}