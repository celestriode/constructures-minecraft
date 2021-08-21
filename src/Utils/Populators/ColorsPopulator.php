<?php namespace Celestriode\ConstructuresMinecraft\Utils\Populators;

use Celestriode\ConstructuresMinecraft\Registries\AbstractRegistry;
use Celestriode\ConstructuresMinecraft\Utils\DynamicPopulatorInterface;

/**
 * Sample populator using values from Minecraft: Java Edition version 1.17.1. Avoid using this; it is purely an example.
 *
 * @package Celestriode\ConstructuresMinecraft\Utils\Populators
 */
class ColorsPopulator implements DynamicPopulatorInterface
{
    /**
     * @inheritDoc
     */
    public function populate(AbstractRegistry $registry): void
    {
        $registry->addValues(
            'black',
            'dark_blue',
            'dark_green',
            'dark_aqua',
            'dark_red',
            'dark_purple',
            'gold',
            'gray',
            'dark_gray',
            'blue',
            'green',
            'aqua',
            'red',
            'light_purple',
            'yellow',
            'white'
        );
    }
}