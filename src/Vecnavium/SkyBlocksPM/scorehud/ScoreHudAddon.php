<?php

declare(strict_types = 1);

namespace Vecnavium\SkyBlocksPM\scorehud;

use Ifera\ScoreHud\event\PlayerTagUpdateEvent;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use pocketmine\player\Player as P;
use Vecnavium\SkyBlocksPM\player\Player;
use pocketmine\scheduler\ClosureTask;
use Vecnavium\SkyBlocksPM\scorehud\ScoreHudListener;
use Vecnavium\SkyBlocksPM\skyblock\SkyBlock;
use Vecnavium\SkyBlocksPM\SkyBlocksPM;

class ScoreHudAddon {

    public const ISLAND_NAME = "skyblockspm.name";
    public const ISLAND_MEMBERS = "skyblockspm.online.members";
    public const NOT_AVBLE = "N/A";

    protected SkyBlocksPM $plugin;


    public function __construct(SkyBlocksPM $plugin) {
        $this->plugin = $plugin;
        $this->registerEvents();
        $this->onLoad();
    }

    public function registerEvents(): void {
        $this->plugin->getServer()->getPluginManager()->registerEvents(new ScoreHudListener($this), $this->plugin);
    }

    public function onLoad(): void {
        $this->repeat(function() {
            foreach ($this->plugin->getServer()->getOnlinePlayers() as $player) {
                if (!$player->isOnline()) {
                    continue;
                }
                (new PlayerTagUpdateEvent($player, new ScoreTag(self::ISLAND_NAME, strval($this->getIslandName($player->getName())))))->call();
                (new PlayerTagUpdateEvent($player, new ScoreTag(self::ISLAND_MEMBERS, strval($this->getOnlineMembers($player->getName())))))->call();
            }
        },
            10);
    }

    public function getIslandName(string $playerName): string {
        $player = $this->getSkyBlockPlayer($playerName);
        if (is_null($player)) {
            return self::NOT_AVBLE;
        }
        $island = $this->getSkyBlock($player);
        //var_dump($island);
        if ($island !== null) {
            return $island->getName();
        }
        return self::NOT_AVBLE;
    }

    public function getOnlineMembers(string $playerName): int|string {
        $player = $this->getSkyBlockPlayer($playerName);
        if (is_null($player)) {
            return self::NOT_AVBLE;
        }
        $island = $this->getSkyBlock($player);

        if ($island !== null) {
            $members = $island->getMembers();
            $onlineMemberCount = 0;

            foreach ($members as $memberName) {
                $member = $this->plugin->getServer()->getPlayerExact($memberName);

                if ($member instanceof P && $member->isOnline()) {
                    $onlineMemberCount++;
                }
            }

            return $onlineMemberCount;
        }

        return self::NOT_AVBLE;
    }

    public function getSkyBlock(Player $player): ?SkyBlock {
        $island = $this->plugin->getSkyBlockManager()->getSkyBlockByUuid($player->getSkyBlock());
        return $island;
    }
    public function getSkyBlockPlayer(string $playerName): ?Player {
        $player = $this->plugin->getPlayerManager()->getPlayer($playerName);
        return $player;
    }

    public function repeat(callable $callback, int $interval): void {
        $task = new ClosureTask($callback);
        $scheduler = $this->plugin->getScheduler();
        $scheduler->scheduleRepeatingTask($task, $interval * 20);
    }
}
