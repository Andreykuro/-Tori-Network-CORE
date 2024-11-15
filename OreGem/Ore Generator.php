<?php

namespace OreGenerator;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\block\Block;
use pocketmine\item\Item;

class OreGenerator extends PluginBase implements Listener {

	public function onEnable() {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
	}

	public function onBlockBreak(BlockBreakEvent $event) {
		$block = $event->getBlock();
		$player = $event->getPlayer();

		// Define the block-ore pairs
		$blockOrePairs = [
			Block::LIGHT_GRAY_GLAZED_TERRACOTTA => Block::IRON_ORE,
			Block::WHITE_GLAZED_TERRACOTTA => Block::COAL_ORE,
			Block::BLACK_GLAZED_TERRACOTTA => Block::GOLD_ORE,
			Block::RED_GLAZED_TERRACOTTA => Block::DIAMOND_ORE
		];

		// Check if the broken block is in the block-ore pairs
		if (isset($blockOrePairs[$block->getId()])) {
			// Generate the corresponding ore
			$ore = $blockOrePairs[$block->getId()];
			$player->getInventory()->addItem(Item::get($ore));
			$player->sendMessage("You got a " . $ore->getName() . "!");
		}
	}
}
