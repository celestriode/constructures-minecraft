<?php namespace Celestriode\ConstructuresMinecraft\Utils;

use Celestriode\ConstructuresMinecraft\Registries\AbstractRegistry;

/**
 * A populator adds values to a registry if the registry wasn't already populated. Population occurs on-demand, which
 * can save a lot of memory usage as some registries for Minecraft are quite dense. Some populators are included in this
 * library for example purposes and should not be relied upon as there's no guarantee that they will be kept up-to-date.
 * As well, an up-to-date registry may not be desirable in the event that registries can be populated using older
 * versions.
 *
 * @package Celestriode\ConstructuresMinecraft\Utils
 */
interface DynamicPopulatorInterface
{
    /**
     * Takes in a registry and populates it with values.
     *
     * @param AbstractRegistry $registry
     * @return void
     */
    public function populate(AbstractRegistry $registry): void;
}