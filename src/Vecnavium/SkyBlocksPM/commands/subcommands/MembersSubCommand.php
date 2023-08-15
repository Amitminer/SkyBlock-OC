<?php

declare(strict_types = 1);

namespace Vecnavium\SkyBlocksPM\commands\subcommands;

use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\player\Player as P;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;
use Vecnavium\SkyBlocksPM\player\Player;
use Vecnavium\SkyBlocksPM\skyblock\SkyBlock;
use Vecnavium\SkyBlocksPM\SkyBlocksPM;
use function in_array;
use function strval;

class MembersSubCommand extends BaseSubCommand {

    protected function prepare(): void {
        $this->setPermission('skyblockspm.members');
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

        if (!($sender instanceof P)) return;
        $skyblockPlayer = $plugin->getPlayerManager()->getPlayer($sender->getName());
        if (!$skyblockPlayer instanceof Player) return;

        $skyblock = $plugin->getSkyBlockManager()->getSkyBlockByUuid($skyblockPlayer->getSkyBlock());
        if (!$skyblock instanceof SkyBlock) {
            $sender->sendMessage($plugin->getMessages()->getMessage('no-sb'));
            return;
        }

        $members = $skyblock->getMembers();
        $leader = $skyblock->getLeader();
        $sender->sendMessage(TextFormat::AQUA . "Members list:");

        foreach ($members as $memberName) {
            $name = TextFormat::BLUE . $memberName;
            $member = $plugin->getServer()->getPlayerExact($memberName);

            if ($member instanceof P) {
                $onlineStatus = $member->isOnline() ? TextFormat::GREEN . "(online)" : TextFormat::RED . "(offline)";
            } else {
                $onlineStatus = TextFormat::RED . "(offline)";
            }

            $leaderStatus = $memberName === $leader ? TextFormat::BOLD . TextFormat::GOLD . "(leader)" . TextFormat::RESET : "";
            $MembersList = str_replace(
                ['{green}', '{red}', '(leader)'],
                [TextFormat::GREEN, TextFormat::RED, $leaderStatus],
                "$name $leaderStatus $onlineStatus"
            );
            $sender->sendMessage($MembersList);
        }
    }
}
