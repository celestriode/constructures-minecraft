<?php namespace Celestriode\ConstructuresMinecraft\Utils\Populators;

use Celestriode\ConstructuresMinecraft\Registries\AbstractRegistry;
use Celestriode\ConstructuresMinecraft\Utils\DynamicPopulatorInterface;

/**
 * Sample populator using values from Minecraft: Java Edition version 1.17.1. Avoid using this; it is purely an example.
 *
 * @package Celestriode\ConstructuresMinecraft\Utils\Populators
 */
class KeybindsPopulator implements DynamicPopulatorInterface
{
    /**
     * @inheritDoc
     */
    public function populate(AbstractRegistry $registry): void
    {
        $registry->addValues(
            'key.forward',
            'key.left',
            'key.back',
            'key.right',
            'key.jump',
            'key.sneak',
            'key.sprint',
            'key.inventory',
            'key.swapOffhand',
            'key.drop',
            'key.use',
            'key.attack',
            'key.pickItem',
            'key.chat',
            'key.playerlist',
            'key.command',
            'key.socialInteractions',
            'key.screenshot',
            'key.togglePerspective',
            'key.smoothCamera',
            'key.fullscreen',
            'key.spectatorOutlines',
            'key.advancements',
            'key.saveToolbarActivator',
            'key.loadToolbarActivator',
            'key.hotbar.1',
            'key.hotbar.2',
            'key.hotbar.3',
            'key.hotbar.4',
            'key.hotbar.5',
            'key.hotbar.6',
            'key.hotbar.7',
            'key.hotbar.8',
            'key.hotbar.9'
        );
    }
}