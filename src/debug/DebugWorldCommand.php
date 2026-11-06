<?php

declare(strict_types=1);

namespace debug;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use pocketmine\permission\DefaultPermissions;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use pocketmine\world\Position;
use pocketmine\world\WorldCreationOptions;

final class DebugWorldCommand extends Command{

	public const string WORLD_NAME = "debugworld";

	public function __construct(){
		parent::__construct(
			"debugworld",
			"Generate and teleport to the debug block-state world",
			"/debugworld [leave]",
			["dw"]
		);
		$this->setPermission(DefaultPermissions::ROOT_OPERATOR);
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) : void{
		if(!$sender instanceof Player){
			return;
		}

		$worldManager = Server::getInstance()->getWorldManager();
		if(($args[0] ?? "") === "leave"){
			$default = $worldManager->getDefaultWorld();
			if($default === null){
				return;
			}
			$sender->teleport($default->getSafeSpawn());
			return;
		}

		if(!$worldManager->isWorldGenerated(self::WORLD_NAME)){
			$sender->sendMessage(TextFormat::YELLOW . "Generating");

			$options = WorldCreationOptions::create()
				->setGeneratorClass(DebugGenerator::class)
				->setSeed(0)
				->setSpawnPosition(new Vector3(0.5, 61, 0.5));

			if(!$worldManager->generateWorld(self::WORLD_NAME, $options)){
				$sender->sendMessage(TextFormat::RED . "World generation failed.");
				return;
			}
		}

		$world = $worldManager->getWorldByName(self::WORLD_NAME);
		if($world === null){
			if(!$worldManager->loadWorld(self::WORLD_NAME) || ($world = $worldManager->getWorldByName(self::WORLD_NAME)) === null){
				$sender->sendMessage(TextFormat::RED . "Could not load the world.");
				return;
			}
		}

		$sender->teleport(new Position(0.5, 61, 0.5, $world));
	}
}