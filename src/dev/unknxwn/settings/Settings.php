<?php

declare(strict_types=1);

namespace dev\unknxwn\settings;

use dev\unknxwn\Loader;

final class Settings
{

    /** @var Settings|null */
    private static $instance = null;

    /**
     * @return Settings
     */
    public static function get(): Settings
    {
        if (!self::$instance) self::$instance = new self;
        return self::$instance;
    }

    /** @var bool */
    private $showInfoOnPlayerName;

    /** @var bool */
    private $showFightInfoPopupFromPlayer;

    /** @var array */
    private $formatOfInfoInPlayerName;

    /** @var array */
    private $worlds;

    /** @var string */
    private $formatFightInfoPopup;

    public function loadConfig()
    {
        $plugin = Loader::get();

        @mkdir($plugin->getDataFolder());
        $plugin->saveDefaultConfig();

        $config = $plugin->getConfig();

        $this->showInfoOnPlayerName = $config->get('show-info-on-player-name');
        $this->showFightInfoPopupFromPlayer = $config->get('show-fight-info-popup-from-player');
        $this->formatOfInfoInPlayerName = $config->get('format-of-info-in-player-name', []);
        $this->worlds = $config->get('worlds', []);
        $this->formatFightInfoPopup = $config->get('format-fight-info-popup', '');
    }

    /**
     * @return bool
     */
    public function isShowInfoOnPlayerName(): bool
    {
        return $this->showInfoOnPlayerName;
    }

    /**
     * @return bool
     */
    public function isShowFightInfoPopupFromPlayer(): bool
    {
        return $this->showFightInfoPopupFromPlayer;
    }

    /**
     * @param string $world
     * @return bool
     */
    public function isValidWorld(string $world): bool
    {
        return empty($this->worlds) || in_array($world, $this->worlds);
    }

    /**
     * @return array
     */
    public function getFormatOfInfoInPlayerName(): array
    {
        return $this->formatOfInfoInPlayerName;
    }

    /**
     * @return string
     */
    public function getFormatFightInfoPopup(): string
    {
        return $this->formatFightInfoPopup;
    }
}
