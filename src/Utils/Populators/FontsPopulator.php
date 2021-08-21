<?php namespace Celestriode\ConstructuresMinecraft\Utils\Populators;

use Celestriode\ConstructuresMinecraft\Registries\AbstractRegistry;
use Celestriode\ConstructuresMinecraft\Utils\DynamicPopulatorInterface;

/**
 * Sample populator using values from Minecraft: Java Edition version 1.17.1. Avoid using this; it is purely an example.
 *
 * @package Celestriode\ConstructuresMinecraft\Utils\Populators
 */
class FontsPopulator implements DynamicPopulatorInterface
{
    /**
     * @inheritDoc
     */
    public function populate(AbstractRegistry $registry): void
    {
        $registry->addValues(
            'minecraft:default',
            'minecraft:alt',
            'minecraft:uniform'
        );
    }
}