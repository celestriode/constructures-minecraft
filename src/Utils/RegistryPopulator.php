<?php namespace Celestriode\ConstructuresMinecraft\Utils;

use Celestriode\ConstructuresMinecraft\Registries\AbstractRegistry;

/**
 * A static class that is used to populate registries dynamically. This means there's no need to waste memory if a
 * registry is not being used when validating input.
 *
 * @package Celestriode\ConstructuresMinecraft\Utils
 */
class RegistryPopulator
{
    /**
     * @var array Mapping of class name to populator.
     */
    private static $populators = [];

    /**
     * Stores a dynamic resource populator for later.
     *
     * @param string $className The name of the registry class that will be populated later.
     * @param DynamicPopulatorInterface $populator The populator to add.
     */
    public static function addDynamicPopulator(string $className, DynamicPopulatorInterface $populator): void
    {
        self::$populators[$className][] = $populator;
    }

    /**
     * Populates resource registries dynamically. That is, rather than having registries populated at all times and
     * wasting memory when they're not used, registries are only populated on-demand. This method will run those
     * on-demand methods.
     *
     * @param AbstractRegistry $registry The registry to populate.
     */
    public static function populateRegistryDynamically(AbstractRegistry $registry): void
    {
        // If no populators are stored, skip.

        if (!isset(self::$populators[get_class($registry)])) {

            return;
        }

        // Cycle through all the populators and collect their values.

        /** @var DynamicPopulatorInterface $populator */
        foreach (self::$populators[get_class($registry)] as $populator) {

            $populator->populate($registry);
        }
    }
}