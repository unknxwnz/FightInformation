<?php

declare(strict_types=1);

namespace dev\unknxwn;

use dev\unknxwn\schedule\FightInfoSchedule;
use dev\unknxwn\settings\Settings;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;

/**
 * @author Unknxwnz <Unknown#4000 | https://github.com/unknxwnz>
 * @copyright 2024 Unknxwnz - Todos os direitos reservados.
 */

final class Loader extends PluginBase
{

    /** @var Loader|null */
    private static $instance = null;

    /**
     * @return Loader
     */
    public static function get(): Loader
    {
        return self::$instance;
    }

    /** @var PureChat|null */
    private $pureChatPlugin;

    /** @var array[] */
    private $clicks = [];

    /** @var array */
    private $combo = [];

    public function onLoad()
    {
        self::$instance = $this;
        Settings::get()->loadConfig();
    }

    public function onEnable()
    {
        $server = $this->getServer();
        $server->getPluginManager()->registerEvents(new EventListener, $this);
        if (Settings::get()->isShowInfoOnPlayerName()) {
            $server->getScheduler()->scheduleRepeatingTask(new FightInfoSchedule, 20);
        }

        $this->pureChatPlugin = $server->getPluginManager()->getPlugin('PureChat') ?? null;
    }

    /**
     * @param Player $player
     * @return string
     */
    public function getPlayerNameTag(Player $player)
    {
        if (!$this->pureChatPlugin) {
            // Not using a supported tag plugin
            return $player->getName();
        }

        $levelName = $this->pureChatPlugin->getConfig()->get('enable-multiworld-support') ? $player->getLevel()->getName() : null;

        return $this->pureChatPlugin->getNameTag($player, $levelName);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function removePlayerFightInfo(Player $player)
    {
        $playerName = strtolower($player->getName());
        unset($this->clicks[$playerName], $this->combo[$playerName]);
    }

    /**
     * @param Player $player
     * @return void
     */
    public function addClicks(Player $player)
    {
        $playerName = strtolower($player->getName());
        $currentTime = time();

        list($clicks, $lastTime) = $this->clicks[$playerName] ?? [0, $currentTime];

        if ($lastTime !== $currentTime) {
            $clicks = 0;
        }

        $clicks++;
        $this->clicks[$playerName] = [$clicks, $currentTime];
    }

    /**
     * @param Player $player
     * @return int
     */
    public function getClicks(Player $player): int
    {
        $playerName = strtolower($player->getName());
        $currentTime = time();
        list($clicks, $lastTime) = $this->clicks[$playerName] ?? [0, $currentTime];

        return $lastTime === $currentTime ? $clicks : 0;
    }

    /**
     * @param Player $player
     * @return void
     */
    public function addCombo(Player $player)
    {
        $playerName = strtolower($player->getName());
        $this->combo[$playerName] = ($this->combo[$playerName] ?? 0) + 1;
    }

    /**
     * @param Player $player
     * @return void
     */
    public function resetCombo(Player $player)
    {
        $this->combo[strtolower($player->getName())] = 0;
    }

    /**
     * @param Player $player
     * @return int
     */
    public function getCombo(Player $player): int
    {
        return $this->combo[strtolower($player->getName())] ?? 0;
    }
}
