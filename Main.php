<?php

namespace PvPCore;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class Main extends PluginBase implements Listener {

    private array $duels = [];

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info(TextFormat::GREEN . "PvPCore enabled!");
    }

    public function onInteract(PlayerInteractEvent $event): void {
        // Handle interactions if needed
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if ($command->getName() === "duel") {
            if (!$sender instanceof Player) {
                $sender->sendMessage(TextFormat::RED . "This command can only be used in-game.");
                return true;
            }

            if (count($args) < 1) {
                $sender->sendMessage(TextFormat::RED . "Usage: /duel <player>");
                return true;
            }

            $targetName = array_shift($args);
            $target = $this->getServer()->getPlayer($targetName);

            if ($target === null) {
                $sender->sendMessage(TextFormat::RED . "Player not found.");
                return true;
            }

            $this->startDuel($sender, $target);
            return true;
        }
        return false;
    }

    private function startDuel(Player $player1, Player $player2): void {
        $this->duels[$player1->getName()] = $player2->getName();
        $this->duels[$player2->getName()] = $player1->getName();

        // Give kits
        $this->giveKit($player1);
        $this->giveKit($player2);

        $player1->sendMessage(TextFormat::GREEN . "Duel started with " . $player2->getName() . "!");
        $player2->sendMessage(TextFormat::GREEN . "Duel started with " . $player1->getName() . "!");
    }

    private function giveKit(Player $player): void {
        $player->getInventory()->clearAll();
        $player->getInventory()->addItem(Item::get(Item::DIAMOND_SWORD));
        $player->getInventory()->addItem(Item::get(Item::ENDER_PEARL, 0, 16));
        $player->getInventory()->addItem(Item::get(Item::POTION, 0, 34)); // Instant Heal Potions
        $player->sendMessage(TextFormat::YELLOW . "You have received your duel kit!");
    }
}
