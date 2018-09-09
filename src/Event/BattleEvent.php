<?php


namespace App\Event;

use App\Entity\Unit;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\Event;

class BattleEvent extends Event
{
    const UNIT_KILLED = 'battle.unit.killed';
    const UNIT_DAMAGED = 'battle.unit.damaged';
    const ROUND_FINISHED = 'battle.round.finished';
    const BATTLE_STARTING = 'battle.starting';
    const BATTLE_FINISHED = 'battle.finished';

    /** @var Unit */
    public $unit1;

    /** @var Unit */
    public $unit2;

    /** @var Unit */
    public $damagee;

    /** @var Unit */
    public $damager;

    /** @var 1|2 */
    public $armyId;

    /** @var null */
    public $roundId;

    /** @var Unit[] */
    public $army1;

    /** @var Unit[] */
    public $army2;

    /** @var OutputInterface */
    public $output;

    /** @var int */
    public $damage;

    /** @var int */
    public $mitigated;

    public function __construct(array $params)
    {
        foreach ($params as $key => $value) {
            if (!property_exists($this, $key)) {
                throw new \LogicException(sprintf('Attempting to set undefined property %s on %s',
                        $key, get_class($this))
                );
            }
            $this->$key = $value;
        }
    }
}