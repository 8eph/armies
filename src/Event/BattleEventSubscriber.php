<?php

namespace App\Event;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class BattleEventSubscriber implements EventSubscriberInterface
{

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            BattleEvent::UNIT_DAMAGED => 'printUnitDamaged',
            BattleEvent::UNIT_KILLED => 'printUnitKilled',
            BattleEvent::ROUND_FINISHED => 'printRoundFinished',
            BattleEvent::BATTLE_STARTING => 'printBattleStarted',
            BattleEvent::BATTLE_FINISHED => 'printBattleFinished',
        ];
    }

    public function printUnitDamaged(BattleEvent $battleEvent)
    {
        $backgroundColourStart = $battleEvent->armyId === 1 ? '<fg=cyan>' : '<fg=blue>';
        $backgroundColourEnd = $battleEvent->armyId === 1 ? '</>' : '</>';
        $hpRemaining = $battleEvent->damagee->getHitpoints() - $battleEvent->damage;

        // just showing off heredoc syntax, sprintf is still more readable in this case
        $battleEvent->output->writeln(<<<HD
{$backgroundColourStart}<army {$battleEvent->armyId}>\t{$battleEvent->damager->getName()} dealt {$battleEvent->damage} damage to {$battleEvent->damagee->getName()} 
\t\t({$battleEvent->mitigated} mitigated by armor, {$hpRemaining} hp remaining){$backgroundColourEnd}
HD
        );
    }

    public function printUnitKilled(BattleEvent $battleEvent)
    {
        // ideally you'd use the console output instead of raw printing
        printf('<army %s>%s%s was slain by %s.%s', $battleEvent->armyId, "\t", $battleEvent->damagee->getName(), $battleEvent->damager->getName(), PHP_EOL);
    }

    public function printRoundFinished(BattleEvent $event)
    {
        $event->output->writeln(
            sprintf('<comment><round %s>%sfinished</comment>', $event->roundId, "\t")
        );
    }

    public function printBattleStarted(BattleEvent $event)
    {
        $army1 = $event->army1;
        $army2 = $event->army2;
        $output = $event->output;
        $output->writeln(sprintf('<info><army 1>%s %s</info>%s', "\t", 'starting the battle with:', PHP_EOL));

        $army1UnitList = $this->countUnits($army1);
        $table = (new Table($event->output))->setHeaders(['name', 'number']);

        foreach ($army1UnitList as $unitName => $unitNumber) {
            $table->addRow([$unitName, $unitNumber]);
        }

        $table->render();

        $event->output->writeln(sprintf('<info><army 2>%s %s</info>%s', "\t", 'starting the battle with:', PHP_EOL));

        $army2UnitList = $this->countUnits($army2);
        $table = (new Table($event->output))->setHeaders(['name', 'number']);

        foreach ($army2UnitList as $unitName => $unitNumber) {
            $table->addRow([$unitName, $unitNumber]);
        }

        $table->render();
    }

    public function printBattleFinished(BattleEvent $event)
    {
        $army1 = $event->army1;
        $army2 = $event->army2;
        $output = $event->output;

        if (!empty($army1) && !empty($army2)) {
            $output->writeln('The results of the battle were inconclusive!');
        }

        if (!empty($army1)) {
            $output->writeln(sprintf('%s<info><army 1>%s %s</info>%s', PHP_EOL, "\t", 'ending the battle with:', PHP_EOL));

            $army1UnitList = $this->countUnits($army1);
            $table = (new Table($event->output))->setHeaders(['name', 'number']);

            foreach ($army1UnitList as $unitName => $unitNumber) {
                $table->addRow([$unitName, $unitNumber]);
            }

            $table->render();

            if (empty($army2)) {
                $output->writeln('Army 1 won!');

                return;
            }
        }

        if (!empty($army2)) {
            $event->output->writeln(sprintf('<info><army 2>%s%s</info>%s', "\t", 'ending the battle with:', PHP_EOL));

            $army2UnitList = $this->countUnits($army2);
            $table = (new Table($event->output))->setHeaders(['name', 'number']);

            foreach ($army2UnitList as $unitName => $unitNumber) {
                $table->addRow([$unitName, $unitNumber]);
            }

            $table->render();

            if (empty($army1)) {
                $output->writeln('Army 2 won!');

                return;
            }
        }

        if (empty($army1) && empty($army2)) {
            $output->writeln('It was a massacre. Everyone is dead. DRAW!');
        }
    }

    private function countUnits(array $army)
    {
        $units = [];

        foreach ($army as $unit) {
            if (!isset($units[$unit->getName()])) {
                $units[$unit->getName()] = 0;
            }

            ++$units[$unit->getName()];
        }

        return $units;
    }
}