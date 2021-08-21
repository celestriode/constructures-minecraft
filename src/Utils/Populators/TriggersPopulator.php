<?php namespace Celestriode\ConstructuresMinecraft\Utils\Populators;

use Celestriode\ConstructuresMinecraft\Registries\AbstractRegistry;
use Celestriode\ConstructuresMinecraft\Utils\DynamicPopulatorInterface;

/**
 * Sample populator using values from Minecraft: Java Edition version 1.17.1. Avoid using this; it is purely an example.
 *
 * @package Celestriode\ConstructuresMinecraft\Utils\Populators
 */
class TriggersPopulator implements DynamicPopulatorInterface
{
    /**
     * @inheritDoc
     */
    public function populate(AbstractRegistry $registry): void
    {
        $data = json_decode(file_get_contents(__DIR__ . '/data/triggers.json'));

        $registry->addValues(...$data);
    }
}