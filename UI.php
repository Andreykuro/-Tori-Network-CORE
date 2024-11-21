<?php

namespace YourPluginNamespace;

use pocketmine\plugin\PluginBase;
use pocketmine\player\Player;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\form\Form;
use pocketmine\form\SimpleForm;
use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener {

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        $this->showGameModeSelector($player);
    }

    public function showGameModeSelector(Player $player): void {
        $form = new SimpleForm(function (Player $player, $data) {
            if ($data === null) {
                return; // User closed the form
            }

            switch ($data) {
                case 0:
                    $player->setGamemode(0); // Survival
                    $player->sendMessage(TextFormat::GREEN . "You have selected Survival mode.");
                    break;
                case 1:
                    $player->setGamemode(1); // Creative
                    $player->sendMessage(TextFormat::GREEN . "You have selected Creative mode.");
                    break;
                case 2:
                    $player->setGamemode(2); // Adventure
                    $player->sendMessage(TextFormat::GREEN . "You have selected Adventure mode.");
                    break;
                case 3:
                    $player->setGamemode(3); // Spectator
                    $player->sendMessage(TextFormat::GREEN . "You have selected Spectator mode.");
                    break;
            }
        });

        $form->setTitle("Select Game Mode");
        $form->setContent("Choose your game mode:");
        $form->addButton("Survival");
        $form->addButton("Creative");
        $form->addButton("Adventure");
        $form->addButton("Spectator");

        $player->sendForm($form);
    }
}
