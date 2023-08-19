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
use Vecnavium\SkyBlocksPM\skyblock\SkyBlockRanks;
use Vecnavium\SkyBlocksPM\SkyBlocksPM;

class ScoreHudAddon {

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
        $duration = $this->getUpdateDuration();
        $this->repeat(function() use ($duration): {
            foreach ($this->plugin->getServer()->getOnlinePlayers() as $player) {
                if (!$player->isOnline()) {
                    continue;
                }
                (new PlayerTagUpdateEvent($player, new ScoreTag(ScoreHudTags::ISLAND_NAME, strval($this->getIslandName($player->getName())))))->call();
                (new PlayerTagUpdateEvent($player, new ScoreTag(ScoreHudTags::ISLAND_MEMBERS, strval($this->getOnlineMembers($player->getName())))))->call();
                (new PlayerTagUpdateEvent($player,new ScoreTag(ScoreHudTags::PLAYER_RANK,strval($this->getPlayerRank($player->getName())))))->call();
            }
        },
            $duration);
    }
    
    public function getUpdateDuration(): int {
        $isEnabled = $this->plugin->getConfig()->get("scorehud");
        if ($isEnabled === true) {
            $duration = $this->plugin->getConfig()->get("scorehud-tag-update-duration");
            return $duration;
        }
    }
    
    public function getPlayerRank(string $playerName): string{
       $player = $this->getSkyBlockPlayer($playerName);
        if (is_null($player)) {
            return ScoreHudTags::NOT_AVBLE;
        }
        $island = $this->getSkyBlock($player);
        if ($island === null) {
            return ScoreHudTags::NOT_AVBLE;
        }
        $managers = $island->getManagers();
        if(in_array($playerName,$managers)){
            return SkyBlockRanks::MANAGER;
        } elseif ($island->getLeader() === $playerName) {
            return SkyBlockRanks::LEADER;
        } else {
            return SkyBlockRanks::MEMBER;
        }
    }

    public function getIslandName(string $playerName): string {
        $player = $this->getSkyBlockPlayer($playerName);
        if (is_null($player)) {
            return ScoreHudTags::NOT_AVBLE;
        }
        $island = $this->getSkyBlock($player);
        //var_dump($island);
        if ($island !== null) {
            return $island->getName();
        }
        return ScoreHudTags::NOT_AVBLE;
    }

    public function getOnlineMembers(string $playerName): int|string {
        $player = $this->getSkyBlockPlayer($playerName);
        if (is_null($player)) {
            return ScoreHudTags::NOT_AVBLE;
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

        return ScoreHudTags::NOT_AVBLE;
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
