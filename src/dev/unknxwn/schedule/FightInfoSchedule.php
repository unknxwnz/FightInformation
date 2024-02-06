<?php

declare(strict_types=1);

namespace dev\unknxwn\schedule;

use dev\unknxwn\Loader;
use dev\unknxwn\settings\Settings;
use pocketmine\Player;
use pocketmine\scheduler\Task;

final class FightInfoSchedule extends Task
{

    public function onRun($currentTick)
    {
        $plugin = Loader::get();

        foreach ($plugin->getServer()->getOnlinePlayers() as $player) {
            $settings = Settings::get();

            if (!$settings->isValidWorld($player->getLevel()->getName())) {
                continue;
            }

            $playerTag = $plugin->getPlayerNameTag($player);
            $ping = method_exists(Player::class, 'getPing') ? $player->getPing() : '§c×';

            /** @var string[] */
            $formattedName = str_ireplace(
                [
                    '{name}',
                    '{cps}',
                    '{health}',
                    '{maxhealth}',
                    '{ping}'
                ],
                [
                    $playerTag,
                    $plugin->getClicks($player),
                    $player->getHealth(),
                    $player->getMaxHealth(),
                    $ping
                ],
                $settings->getFormatOfInfoInPlayerName()
            );
            $player->setNameTag(implode("\n", $formattedName));
        }
    }
}
