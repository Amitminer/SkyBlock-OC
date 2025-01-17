<?php
declare(strict_types = 1);

namespace Vecnavium\SkyBlocksPM\scorehud;

use Ifera\ScoreHud\event\TagsResolveEvent;
use pocketmine\event\Listener;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use Ifera\ScoreHud\event\PlayerTagUpdateEvent;
use pocketmine\player\Player;
use Vecnavium\SkyBlocksPM\scorehud\ScoreHudTags;
use Vecnavium\SkyBlocksPM\SkyBlocksPM;
use function count;
use function strval;
use function explode;

class ScoreHudListener implements Listener {

    private ScoreHudAddon $scorehudManager;

    public function __construct(ScoreHudAddon $scorehudManager) {
        $this->scorehudManager = $scorehudManager;
    }
    public function onTagResolve(TagsResolveEvent $event) {
        $tag = $event->getTag();
        $tags = explode('.', $tag->getName(), 2);
        $value = "";
        $playerName = $event->getPlayer()->getName();

        if ($tags[0] !== 'skyblockspm' || count($tags) < 2) return;

        switch ($tags[1]) {

            case ScoreHudTags::ISLAND_NAME:
                $value = $this->scorehudManager->getIslandName($playerName);
                break;

            case ScoreHudTags::ISLAND_MEMBERS:
                $value = $this->scorehudManager->getOnlineMembers($playerName);
                break;
            case ScoreHudTags::PLAYER_RANK:
                $value = $this->scorehudManager->getPlayerRank($playerName);
                break;
        }
        $tag->setValue(strval($value));
    }

}