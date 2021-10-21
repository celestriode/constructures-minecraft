<?php namespace Celestriode\ConstructuresMinecraft\Utils;

/**
 * An enumeration for the various editions of Minecraft. Useful for branching off audits based on edition.
 *
 * Fake enum class since PHP won't have enums until 8.1.
 *
 * @package Celestriode\ConstructuresMinecraft\Utils
 */
abstract class EnumEdition
{
    public const ANY = 0;
    public const JAVA = 1;
    public const BEDROCK = 2;
}