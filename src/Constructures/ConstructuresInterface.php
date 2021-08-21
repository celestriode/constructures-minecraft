<?php namespace Celestriode\ConstructuresMinecraft\Constructures;

use Celestriode\Constructure\Structures\StructureInterface;
use Ramsey\Uuid\UuidInterface;

/**
 * Defines methods required for all expected structures.
 *
 * @package Celestriode\ConstructuresMinecraft\Constructures
 */
interface ConstructuresInterface
{
    /**
     * Returns the expected structure.
     *
     * @return StructureInterface
     */
    public static function getStructure(): StructureInterface;

    /**
     * Returns the UUID of the whole structure.
     *
     * @return UuidInterface
     */
    public static function getUUID(): UuidInterface;
}