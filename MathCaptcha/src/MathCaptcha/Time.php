<?php 

namespace MathCaptcha;

use MathCaptcha\Main;
use pocketmine\scheduler\Task;
use pocketmine\player\Player;

class Time extends Task{
	public function __construct(Main $plugin, $sender){
		$this->plugin = $plugin;
		$this->player = $sender;
	}
	public function onRun(): void{
		$sender = $this->plugin->getServer()->getPlayerExact($this->player);
		if($sender == null) return;
		
		if($this->plugin->soLan($sender) == 1) $sender->kick($this->plugin->getConfig()->get("kick-msg"),true);
		
		if($this->plugin->time($sender) != 0){
			if(time() >= $this->plugin->time($sender)) $sender->kick($this->plugin->getConfig()->get("kick-msg"),true);
		}
		
		
	}
}