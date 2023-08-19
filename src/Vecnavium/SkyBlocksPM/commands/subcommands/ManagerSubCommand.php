<?php

declare(strict_types = 1);

namespace Vecnavium\SkyBlocksPM\commands\subcommands;

use Vecnavium\SkyBlocksPM\libs\CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Vecnavium\SkyBlocksPM\commands\args\PlayerArgument;
use Vecnavium\SkyBlocksPM\player\Player as SBPlayer;
use Vecnavium\SkyBlocksPM\skyblock\SkyBlock;
use Vecnavium\SkyBlocksPM\SkyBlocksPM;
use pocketmine\Server;
use function array_search;
use function in_array;
use function str_replace;
use function strval;

class ManagerSubCommand extends BaseSubCommand {

    protected function prepare(): void {
        $this->setPermission('skyblockspm.manager');
        $this->registerArgument(0, new PlayerArgument('player'));
    }

    /**
    * @param CommandSender $sender
    * @param string $aliasUsed
    * @param array $args
    * @return void
    *
    * @phpstan-ignore-next-line
    */
    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        /** @var SkyBlocksPM $plugin */
        $plugin = $this->getOwningPlugin();

        if (!$sender instanceof Player) {
            return;
        }

        $playerName = $args['player'] instanceof Player ? $args['player']->getName() : strval($args['player']);
        $manager = Server::getInstance()->getPlayerExact($playerName);
        $skyblockPlayer = $plugin->getPlayerManager()->getPlayer($sender->getName());
        if (!$skyblockPlayer instanceof SBPlayer) {
           // var_dump($skyblockPlayer);
            return;
        }

        if ($manager === null) {
            $notfound = $this->getMSG('player-not-online');
            $sender->sendMessage($notfound);
            return;
        }

        if ($manager->getName() === $sender->getName()) {
            $noSelf = $this->getMSG("no-self-manager");
            $sender->sendMessage($noSelf);
            return;
        }

        if ($skyblockPlayer->getSkyBlock() == '') {
            $sender->sendMessage($this->getMSG('no-sb'));
            return;
        }

        $skyblock = $plugin->getSkyBlockManager()->getSkyBlockByUuid($skyblockPlayer->getSkyBlock());
        if ($skyblock instanceof SkyBlock) {
            if ($skyblock->getLeader() !== $sender->getName()) {
                $sender->sendMessage($this->getMSG('no-manager-perm'));
                return;
            }

            $members = $skyblock->getMembers();
            //  var_dump($members);
            $managerName = $manager->getName();

            if (!in_array($managerName, $members, true)) {
                $sender->sendMessage($this->getMSG("not-island-member"));
                return;
            }
            $this->addManager($plugin, $skyblock, $manager, $sender);
        }
    }

    private function addManager(SkyBlocksPM $plugin, SkyBlock $skyblock, Player $manager, Player $player): void {
        $managerName = $manager->getName();
        $playerName = $player->getName();
        $managers = $skyblock->getManagers();

        if (in_array($managerName, $managers, true)) {
            $managersIndex = array_search($managerName, $managers, true);
            if ($managersIndex !== false) {
                unset($managers[$managersIndex]);
                $skyblock->setManagers($managers);
                $this->save($skyblock);
            }
            $message = $this->getReplacedMsg("manager-removed", $managerName);
            $Managermessage = $this->getReplacedMsg("manager-removed-player", $playerName);
        } else {
            $managers[] = $managerName;
            $skyblock->setManagers($managers);
            $message = $this->getReplacedMsg("manager-added", $managerName);
            $Managermessage = $this->getReplacedMsg("manager-added-player", $playerName);
            $this->save($skyblock);
        }

        $manager->sendMessage($Managermessage);
        $player->sendMessage($message);
    }

    private function save(SkyBlock $skyblock): void {
        $skyblock->save();
    }

    private function getReplacedMsg(string $msg, string $playerName): string {
        $message = str_replace('{PLAYER}', $playerName, $this->getMSG($msg));
        return $message;
    }

    private function getMSG(string $msg): string {
        /** @var SkyBlocksPM $plugin */
        $plugin = $this->getOwningPlugin();
        $message = $plugin->getMessages()->getMessage($msg);
        return $message;
    }
}