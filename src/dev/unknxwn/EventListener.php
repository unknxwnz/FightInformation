<?php

declare(strict_types=1);

namespace dev\unknxwn;

use dev\unknxwn\settings\Settings;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\Player;

final class EventListener implements Listener
{

    public function onEntityDamage(EntityDamageEvent $event)
    {
        if ($event instanceof EntityDamageByEntityEvent) {
            if ($event->isCancelled()) return;

            $damager = $event->getDamager();
            $entity = $event->getEntity();

            if ($damager instanceof Player && $entity instanceof Player) {
                $settings = Settings::get();

                if (!$settings->isShowFightInfoPopupFromPlayer()) return;

                if ($settings->isValidWorld($damager->getLevel()->getName())) {
                    $plugin = Loader::get();

                    $plugin->addClicks($damager);
                    $plugin->addCombo($damager);
                    $plugin->resetCombo($entity);

                    $damager->sendPopup(str_ireplace(
                        [
                            '{cps}',
                            '{combo}',
                            '{reach}'
                        ],
                        [
                            $plugin->getClicks($damager),
                            $plugin->getCombo($damager),
                            sprintf('%.2f', $damager->distance($entity))
                        ],
                        $settings->getFormatFightInfoPopup()
                    ));
                }
            }
        }
    }

    public function onPlayerQuit(PlayerQuitEvent $event)
    {
        Loader::get()->removePlayerFightInfo($event->getPlayer());
    }
}
