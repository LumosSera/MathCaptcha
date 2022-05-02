<?php

namespace MathCaptcha;

use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\utils\Config;
use pocketmine\scheduler\ClosureTask;
use pocketmine\scheduler\Task;

class Main extends PluginBase implements Listener{
    public $answer = 0;
    public function onEnable(): void{
        $this->saveDefaultConfig();
        $this->captcha = new Config($this->getDataFolder()."captcha.yml", Config::YAML);
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
    }
    public function onJoin(PlayerJoinEvent $ev){
        $sender = $ev->getPlayer();
        $this->captcha->set($sender->getName(),["solan" => $this->getConfig()->get("number-math") + 1,"time" => time() + $this->getConfig()->get("time-math")]);
        $this->captcha->save();
        $this->MathCaptcha($sender);
        
    }
    
    public function onQuit(PlayerQuitEvent $ev){
        $sender = $ev->getPlayer();
        
        $this->captcha->set($sender->getName(),["solan" => 0,"time" => 0]);
         $this->captcha->save();
        
    }
    public function soLan(Player $sender): int{
        
        return $this->captcha->get($sender->getName())["solan"];
        
    }
    public function time(Player $sender): int{
        
        return $this->captcha->get($sender->getName())["time"];
            
        
        
    }
    public function MathCaptcha($sender){
        $this->answer = 0;
        $a = mt_rand(1,3);
        switch($a){
            case 1:
                $math = "+";
                break;
            case 2:
                $math = "-";
                break;
            case 3:
                $math = "x";
                break;
        }
        $poser1 = mt_rand(1,10);
        $poser2 = mt_rand(1,10);
        switch($math){
            case "+":
                $this->answer = $poser1 + $poser2;
                break;
            case "-":
                $this->answer = $poser1 - $poser2;
                break;
            case "x":
                $this->answer = $poser1 * $poser2;
                break;

        }
        $this->getScheduler()->scheduleRepeatingTask(new Time($this, $sender->getName()), 20);
        $formapi = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $formapi->createCustomForm(function (Player $sender, ?array $data){
            if($data === null) return $this->MathCaptcha($sender);
                        if($data[0] == $this->answer){
                            $sender->sendMessage($this->getConfig()->get("captcha-success"));
                            $this->captcha->set($sender->getName(),["solan" => 0,"time" => 0]);
                            $this->captcha->save();
                        }else{
                            $this->captcha->set($sender->getName(), ["solan" => $this->captcha->get($sender->getName())["solan"] - 1, "time" => $this->captcha->get($sender->getName())["time"]]);
                            $this->captcha->save();
                            return $this->MathCaptcha($sender);
                        }
                    
                    
        }); 
        $form->setTitle($this->getConfig()->get("title"));
        $form->addInput($this->getConfig()->get("content")." ".$poser1." ".$math." ".$poser2."\n".$this->getConfig()->get("text")." ".$this->captcha->get($sender->getName())["solan"] - 1, $this->getConfig()->get("placeholder-input"));
        $form->sendToPlayer($sender);
        return $form;
    }
}