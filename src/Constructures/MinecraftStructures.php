<?php namespace Celestriode\ConstructuresMinecraft\Constructures;

use Celestriode\Constructure\Structures\StructureInterface;
use Celestriode\ConstructuresMinecraft\Constructures\Minecraft\Java\DataPacks\Advancements;
use Celestriode\ConstructuresMinecraft\Constructures\Minecraft\Java\TextComponents as JavaTextComponents;
use Celestriode\ConstructuresMinecraft\Constructures\Minecraft\Bedrock\TextComponents as BedrockTextComponents;
use Celestriode\ConstructuresMinecraft\Constructures\Minecraft\Java\TargetSelectors as JavaTargetSelectors;
use Celestriode\ConstructuresMinecraft\Constructures\Minecraft\Bedrock\TargetSelectors as BedrockTargetSelectors;
use Celestriode\ConstructuresMinecraft\Exceptions\RuntimeException;

/**
 * Registry of expected structures. Expected structures have a one-to-many relationship, so singletons are preferred.
 *
 * @package Celestriode\ConstructuresMinecraft\Constructures
 */
final class MinecraftStructures
{
    /**
     * @var string[] The class names of the registered structures that implement ConstructuresInterface.
     */
    private static $structures = [
        JavaTextComponents::class,
        Advancements::class,
        BedrockTextComponents::class,
        JavaTargetSelectors::class,
        BedrockTargetSelectors::class
    ];

    /**
     * @var array Holds StructureInterface singletons of expected structures, created when necessary.
     */
    private static $instances = [];

    public static function register(string $className): void
    {
        if (!in_array($className, self::$structures) && is_subclass_of($className, ConstructuresInterface::class)) {

            self::$structures[] = $className;
        }
    }

    /**
     * Removes all structures and instances from the registry. Useful if overriding default structures.
     */
    public static function clearRegistry(): void
    {
        self::$structures = [];
        self::$instances = [];
    }

    /**
     * Gets a singleton of an expected structure.
     *
     * @param string $className The name of the ConstructuresInterface class that holds the expected structure.
     * @return StructureInterface
     * @throws RuntimeException
     */
    public static function get(string $className): StructureInterface
    {
        if (!in_array($className, self::$structures) || !is_subclass_of($className, ConstructuresInterface::class)) {

            throw new RuntimeException('Structure "' . $className . '" is not registered or is invalid');
        }

        /** @var ConstructuresInterface|string $className */

        // If the structure wasn't obtained, obtain, store, and return it.

        if (!isset(self::$instances[$className])) {

            return self::$instances[$className] = $className::getStructure();
        }

        // Return the stored structure.

        return self::$instances[$className];
    }
}