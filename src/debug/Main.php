<?php

declare(strict_types=1);

namespace debug;

use pocketmine\plugin\PluginBase;
use pocketmine\world\generator\GeneratorManager;

final class Main extends PluginBase{

	protected function onLoad() : void{
		GeneratorManager::getInstance()->addGenerator(
			DebugGenerator::class,
			"debug",
			fn(string $preset) => null
		);
	}

	protected function onEnable() : void{
		$this->getServer()->getCommandMap()->register("debuggen", new DebugWorldCommand());
	}
}