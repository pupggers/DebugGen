<?php

declare(strict_types=1);

namespace debug;

use pocketmine\block\Block;
use pocketmine\block\RuntimeBlockStateRegistry;
use pocketmine\block\VanillaBlocks;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\Generator;

final class DebugGenerator extends Generator{

	private const int BLOCK_Y = 61;
	private const int FLOOR_Y = 60;
	private const int BLOCK_SPACING = 3;

	/** @var Block[] */
	private array $states = [];

	private int $gridWidth;

	public function __construct(int $seed, string $preset){
		parent::__construct($seed, $preset);

		$states = RuntimeBlockStateRegistry::getInstance()
			->getAllKnownStates();

		ksort($states, SORT_NUMERIC);

		foreach($states as $state){
			$this->states[] = clone $state;
		}

		$this->gridWidth = (int) ceil(
			sqrt(count($this->states))
		);
	}

	public function generateChunk(
		ChunkManager $world,
		int $chunkX,
		int $chunkZ
	) : void{

		$startX = $chunkX << 4;
		$startZ = $chunkZ << 4;

		$bedrock = VanillaBlocks::BEDROCK();

		for($x = 0; $x < 16; ++$x){
			for($z = 0; $z < 16; ++$z){

				$worldX = $startX + $x;
				$worldZ = $startZ + $z;

				$world->setBlockAt(
					$worldX,
					self::FLOOR_Y,
					$worldZ,
					$bedrock
				);

				if($worldX < 0 || $worldZ < 0){
					continue;
				}

				if(
					$worldX % self::BLOCK_SPACING !== 1 ||
					$worldZ % self::BLOCK_SPACING !== 1
				){
					continue;
				}

				$gridX = intdiv(
					$worldX,
					self::BLOCK_SPACING
				);

				$gridZ = intdiv(
					$worldZ,
					self::BLOCK_SPACING
				);

				if($gridX >= $this->gridWidth){
					continue;
				}

				$index = (
						$gridZ * $this->gridWidth
					) + $gridX;

				if(!isset($this->states[$index])){
					continue;
				}

				$world->setBlockAt(
					$worldX,
					self::BLOCK_Y,
					$worldZ,
					clone $this->states[$index]
				);
			}
		}
	}

	public function populateChunk(
		ChunkManager $world,
		int $chunkX,
		int $chunkZ
	) : void{}
}