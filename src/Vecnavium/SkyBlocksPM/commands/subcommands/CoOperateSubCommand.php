<?php

declare(strict_types = 1);

namespace Vecnavium\SkyBlocksPM\commands\subcommands;

use Vecnavium\SkyBlocksPM\libs\CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player as P;
use Vecnavium\SkyBlocksPM\commands\args\PlayerArgument;
use Vecnavium\SkyBlocksPM\player\Player;
use Vecnavium\SkyBlocksPM\skyblock\SkyBlock;
use Vecnavium\SkyBlocksPM\SkyBlocksPM;
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

        if (!$sender instanceof P) return;

        $toCoopPlayer = $plugin->getPlayerManager()->getPlayer(($args['player'] instanceof P ? $args['player']->getName() : strval($args['player'])));
        $skyblockPlayer = $plugin->getPlayerManager()->getPlayer($sender->getName());
        if (!$skyblockPlayer instanceof Player) return;

        if (!$toCoopPlayer instanceof Player) {
            $sender->sendMessage($plugin->getMessages()->getMessage('player-not-online'));
            return;
        }
        if ($skyblockPlayer->getSkyBlock() == '') {
            $sender->sendMessage($plugin->getMessages()->getMessage('no-sb'));
            return;
        }
        $skyblock = $plugin->getSkyBlockManager()->getSkyBlockByUuid($skyblockPlayer->getSkyBlock());
        if ($skyblock instanceof SkyBlock) {
            if ($skyblock->getLeader() !== $sender->getName()) {
                $sender->sendMessage($plugin->getMessages()->getMessage('no-kick'));
                return;
            }
            $members = $skyblock->getMembers();

            if (!in_array($toCoopPlayer->getName(), $members, true)) {
                $sender->sendMessage($plugin->getMessages()->getMessage('not-member'));
                return;
            }
            //TODO: Adddin logic to coop player.
        }
    }
}