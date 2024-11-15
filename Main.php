<?php

namespace CustomGameModeSelector;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\utils\TextFormat;
use pocketmine\form\Form;
use pocketmine\form\SimpleForm;
use muqsit\invmenu\InvMenu; // Ensure you have the FormsAPI installed

class Main extends PluginBase implements Listener {

    private array $gameModes;

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        // Load custom game modes from plugin.yml
        $this->gameModes = $this->getConfig()->get("gamemodes", [
            "Survival",
            "Creative",
            "Adventure"
        ]);

        $this->getLogger()->info(TextFormat::GREEN . "CustomGameModeSelector enabled!");
    }

    public function onInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        if ($event->getItem()->getId() === Item::DIAMOND) { // Change to any item you want to trigger the GUI
            $this->showGameModeMenu($player);
        }
    }

    public function showGameModeMenu($player): void {
        $form = new SimpleForm(function($player, $data) {
            if ($data === null) return; // Player closed the form
            $selectedMode = $this->gameModes[$data];

            switch ($selectedMode) {
                case "Survival":
                    $player->setGamemode(0); // Survival
                    break;
                case "Creative":
                    $player->setGamemode(1); // Creative
                    break;
                case "Adventure":
                    $player->setGamemode(2); // Adventure
                    break;
                default:
                    $player->sendMessage(TextFormat::RED . "Invalid game mode selected.");
                    return;
            }
            $player->sendMessage(TextFormat::GREEN . "You are now in " . $selectedMode . " mode!");
        });

        $form->setTitle("Select Game Mode");

        foreach ($this->gameModes as $mode) {
            $form->addButton($mode);
        }

        $player->sendForm($form);
    }
}