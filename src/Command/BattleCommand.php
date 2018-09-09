<?php

namespace App\Command;

use App\Service\ArmyManager;
use App\Service\BattleManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BattleCommand extends Command
{
    private $armyManager;
    private $battleManager;

    public function __construct(ArmyManager $armyManager, BattleManager $battleManager)
    {
        $this->armyManager = $armyManager;
        $this->battleManager = $battleManager;

        parent::__construct();
    }

    public function configure()
    {
        $this
            ->setName('armies:battle')
            ->setDescription('Battles two randomly created armies. See src/Configuration/config.yml for the army composition.')
            ->addArgument('army1Size', InputArgument::REQUIRED, 'The size of the first army.')
            ->addArgument('army2Size', InputArgument::REQUIRED, 'The size of the second army.')
            ->addOption('rounds', '-r', InputOption::VALUE_OPTIONAL, 'The number of rounds to fight.', 5)
        ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $maxRounds = $input->getOption('rounds');

        if ($maxRounds < 1) {
            throw new \LogicException('The armies just stand there and curse at each other.'.
            PHP_EOL.
            'One of them is definitely the moral victor.');
        }

        $this->battleManager->battle(
            $this->armyManager->generateArmy($input->getArgument('army1Size')),
            $this->armyManager->generateArmy($input->getArgument('army2Size')),
            $maxRounds,
            $output
        );
    }
}