<?php

namespace PvPCore;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\level\Position;
use pocketmine\form\Form;
use pocketmine\form\SimpleForm;

class Main extends PluginBase implements Listener
{
    private array $duels = [];
    private array $invites = [];

    public function onEnable(): void
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info(TextFormat::GREEN . "PvPCore enabled!");
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool
    {
        if ($command->getName() === "duel") {
            if (!$sender instanceof Player) {
                $sender->sendMessage(TextFormat::RED . "This command can only be used in-game.");
                return true;
            }

            if (count($args) < 1) {
                $this->showMainMenu($sender);
                return true;
            }

            if (strtolower($args[0]) === "accept" && isset($args[1])) {
                return $this->acceptDuel($sender, $args[1]);
            }

            return $this->sendDuelInvite($sender, $args[0]);
        }
        return false;
    }

    private function showMainMenu(Player $player): void {
        $form = new SimpleForm(function (Player $player, ?int $data) {
            if ($data === null) return; // User closed the form
            switch ($data) {
                case 0: // Invites
                    $this->showInvites($player);
                    break;
                case 1: // Gamemodes
                    $this->showGameModes($player);
                    break;
            }
        });

        $form->setTitle("Duel Menu");
        $form->addButton("Invites");
        $form->addButton("Gamemodes");

        $player->sendForm($form);
    }

    private function showInvites(Player $player): void {
        // Here you can implement the logic to show current invites
        $player->sendMessage(TextFormat::YELLOW . "Current invites: " . implode(", ", $this->invites));
    }

    private function showGameModes(Player $player): void {
        $form = new SimpleForm(function (Player $player, ?int $data) {
            if ($data === null) return; // User closed the form
            switch ($data) {
                case 0: // Nodebuff
                    $player->sendMessage(TextFormat::GREEN . "Nodebuff mode selected!");
                    // Here you can implement logic to set the game mode to Nodebuff
                    break;
            }
        });

        $form->setTitle("Gamemodes");
        $form->addButton("Nodebuff");

        $player->sendForm($form);
    }

    private function sendDuelInvite(Player $sender, string $targetName): bool {
        $target = $this->getServer()->getPlayer($targetName);

        if ($target === null) {
            $sender->sendMessage(TextFormat::RED . "Player not found.");
            return true;
        }

        if (isset($this->invites[$target->getName()])) {
            $sender->sendMessage(TextFormat::RED . "Player already has a pending duel invite.");
            return true;
        }

        $this->invites[$target->getName()] = $sender->getName();
        $target->sendMessage(TextFormat::GREEN . $sender->getName() . " has invited you to a duel! Use /duel accept " . $sender->getName() . " to accept.");
        $sender->sendMessage(TextFormat::YELLOW . "Duel invite sent to " . $target->getName() . "!");
        return true;
    }

    private function acceptDuel(Player $player, string $inviterName): bool {
        if (!isset($this->invites[$player->getName()]) || $this->invites[$player->getName()] !== $inviterName) {
            $player->sendMessage(TextFormat::RED . "You have no duel invite from " . $inviterName . ".");
            return true;
        }

        $inviter = $this->getServer()->getPlayer($inviterName);
        if ($inviter === null) {
            $player->sendMessage(TextFormat::RED . "The player who invited you is no longer online.");
            return true;
        }

        unset($this->invites[$player->getName()]); // Remove the invite
        $this->startDuel($inviter, $player);
        return true;
    }

    private function startDuel(Player $player1, Player $player2): void {
        // Teleport players to arena (you can change the coordinates)
        $arenaPosition = new Position(100, 64, 100, $this->getServer()->getDefaultLevel());
        $player1->teleport($arenaPosition);
        $player2->teleport($arenaPosition);

        // Give kits
        $this->giveKit($player1);
        $this->giveKit($player2);

        $player1->sendMessage(TextFormat::YELLOW . "Duel started with " . $player2->getName() . "!");
        $player2->sendMessage(TextFormat::YELLOW . "Duel started with " . $player1->getName() . "!");
    }

    private function giveKit(Player $player): void {

        $player->getInventory()->clearAll();

        // Set the items
        $sword = Item::get(Item::DIAMOND_SWORD); // Diamond Sword
        $enderPearls = Item::get(Item::ENDER_PEARL, 0, 16); // 16 Ender Pearls
        $instantHealth = Item::get(Item::INSTANT_HEALTH_II, 0, 34); // 34 Instant Health II Potions
        $instantHealth->setCustomModelData(1);

        // Add items to the hotbar and inventory
        $player->getInventory()->setItem(0, $sword); // 1st slot (hotbar)
        $player->getInventory()->setItem(1, $enderPearls); // 2nd slot (hotbar)

        // Fill the rest of the inventory with Instant Health II Potions
        for ($i = 2; $i < 36; $i++) {
            $player->getInventory()->setItem($i, $instantHealth); // Fill slots 3 to 36
        }

        // Equip full Diamond Armor
        $player->getArmorInventory()->setHelmet(Item::get(Item::DIAMOND_HELMET)); // Diamond Helmet
        $player->getArmorInventory()->setChestplate(Item::get(Item::DIAMOND_CHESTPLATE)); // Diamond Chestplate
        $player->getArmorInventory()->setLeggings(Item::get(Item::DIAMOND_LEGGINGS)); // Diamond Leggings
        $player->getArmorInventory()->setBoots(Item::get(Item::diamond_boots)); // Diamond Boots

        // Optionally, send a message to the player
        $player->sendMessage(TextFormat::GREEN . "You have received your duel kit!");
    }
}