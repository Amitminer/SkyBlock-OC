<?php

declare(strict_types=1);

namespace Vecnavium\SkyBlocksPM\commands;

use Vecnavium\SkyBlocksPM\commands\subcommands\{
    AcceptSubCommand,
    ChatSubCommand,
    CreateSubCommand,
    DeleteSubCommand,
    InviteSubCommand,
    KickSubCommand,
    LeaveSubCommand,
    SettingsSubCommand,
    SetWorldCommand,
    TpSubCommand,
    VisitSubCommand,
    MembersSubCommand
};
use Vecnavium\SkyBlocksPM\libs\CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;
use Vecnavium\SkyBlocksPM\SkyBlocksPM;

class SkyBlockCommand extends BaseCommand {
    
    protected SkyBlocksPM $skyblockspm;
    
    public function __construct(SkyBlocksPM $plugin){
        $this->skyblockspm = $plugin;
        parent::__construct($plugin, 'skyblock', 'The core command for SkyBlocks', ['sb', 'is']);
    }

    public function prepare(): void {
        $this->setPermission('skyblockspm.command');
        $this->registerSubCommand(new AcceptSubCommand($this->skyblockspm, 'accept', 'Accept the incoming invite to a SkyBlock Island'));
        $this->registerSubCommand(new ChatSubCommand($this->skyblockspm, 'chat', 'Chat with your SkyBlock Island members'));
        $this->registerSubCommand(new CreateSubCommand($this->skyblockspm, 'create', 'Create your own SkyBlock Island'));
        $this->registerSubCommand(new DeleteSubCommand($this->skyblockspm, 'delete', 'Delete a users SkyBlock Island', ['disband']));
        $this->registerSubCommand(new KickSubCommand($this->skyblockspm, 'kick', 'Kick a member from your SkyBlock Island'));
        $this->registerSubCommand(new LeaveSubCommand($this->skyblockspm, 'leave', 'Leave your current SkyBlock Island'));
        $this->registerSubCommand(new SettingsSubCommand($this->skyblockspm, 'settings', 'Edit your SkyBlock Island settings'));
        $this->registerSubCommand(new SetWorldCommand($this->skyblockspm, 'setworld', 'Sets the current world as the SkyBlock World which will be copied to newer worlds upon Island creation'));
        $this->registerSubCommand(new TpSubCommand($this->skyblockspm, 'tp', 'Teleport to a users SkyBlock Island', ['go', 'home']));
        $this->registerSubCommand(new InviteSubCommand($this->skyblockspm, 'invite', 'Invites the player to your SkyBlock Island'));
        $this->registerSubCommand(new VisitSubCommand($this->skyblockspm, 'visit', 'Visit a players SkyBlock Island'));
        $this->registerSubCommand(new MembersSubCommand($this->skyblockspm, 'members','Show all the members of a island', ['member']));
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
        $this->sendUsage();
    }
}
