<?php

declare(strict_types = 1);

namespace Vecnavium\SkyBlocksPM\commands\subcommands;

use Vecnavium\SkyBlocksPM\libs\CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use Vecnavium\SkyBlocksPM\commands\args\PlayerArgument;
use Vecnavium\SkyBlocksPM\player\PlayerManager;
use Vecnavium\SkyBlocksPM\player\Player as SBPlayer;
use Vecnavium\SkyBlocksPM\skyblock\SkyBlock;
use Vecnavium\SkyBlocksPM\SkyBlocksPM;
use pocketmine\Server;
use function array_search;
use function in_array;
use function strval;

class CoOperateSubCommand extends BaseSubCommand {

    protected function prepare(): void {
        $this->setPermission('skyblockspm.cooperate');
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
        $coopPlayer = Server::getInstance()->getPlayerExact($playerName);
        $skyblockPlayer = $plugin->getPlayerManager()->getPlayer($sender->getName());
        if (!$skyblockPlayer instanceof SBPlayer) {
            return;
        }

        if ($coopPlayer === null) {
            $sender->sendMessage($this->getMSG('player-not-online'));
            return;
        }

        if ($coopPlayer->getName() === $sender->getName()) {
            $sender->sendMessage($this->getMSG("no-self-coop"));
            return;
        }

        if ($skyblockPlayer->getSkyBlock() == '') {
            $sender->sendMessage($this->getMSG('no-sb'));
            return;
        }

        $skyblock = $plugin->getSkyBlockManager()->getSkyBlockByUuid($skyblockPlayer->getSkyBlock());
        if ($skyblock instanceof SkyBlock) {
            if ($skyblock->getLeader() !== $sender->getName()) {
                $sender->sendMessage($this->getMSG('no-coop-perm'));
                return;
            }

            $members = $skyblock->getMembers();
            $coopPlayerName = $coopPlayer->getName();

            if (in_array($coopPlayerName, $members, true)) {
                $sender->sendMessage($this->getReplacedMsg("already-member", $coopPlayerName));
                return;
            }
            $this->ExecuteCooperatorAction($plugin, $coopPlayer, $sender);
        }
    }

    private function ExecuteCooperatorAction(SkyBlocksPM $plugin, Player $coopPlayer, Player $player): void {
        $coopPlayerName = $coopPlayer->getName();
        $playerName = $player->getName();
        if ($plugin->getPlayerManager()->isPlayerCooperator($coopPlayerName)) {
            $plugin->getPlayerManager()->removeCooperator($coopPlayerName);
            $message = $this->getReplacedMsg("cooperator-removed", $coopPlayerName);
            $coopmessage = $this->getReplacedMsg("removed-coop", $playerName);
        } else {
            $plugin->getPlayerManager()->addCooperator($coopPlayerName);
            $message = $this->getReplacedMsg("cooperator-added", $coopPlayerName);
            $coopmessage = $this->getReplacedMsg("cooperated-player", $playerName);
        }

        $coopPlayer->sendMessage($coopmessage);
        $player->sendMessage($message);
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