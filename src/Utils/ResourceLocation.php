<?php namespace Celestriode\ConstructuresMinecraft\Utils;

use Celestriode\DynamicRegistry\AbstractRegistry;
use Celestriode\Mattock\Parsers\Java\Utils\ResourceLocation as MattockResourceLocation;

/**
 * A representation of resource locations in Minecraft. Used with audits for verifying syntax and values.
 *
 * @package Celestriode\ConstructuresMinecraft\Utils
 */
class ResourceLocation extends MattockResourceLocation
{
    /**
     * Returns whether or not the resource location is present within the given registry.
     *
     * @param AbstractRegistry $registry The registry to match the resource location against.
     * @return bool
     */
    public function validResource(AbstractRegistry $registry): bool
    {
        return $registry->has($this->toString());
    }
}