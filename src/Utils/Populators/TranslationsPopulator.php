<?php namespace Celestriode\ConstructuresMinecraft\Utils\Populators;

use Celestriode\DynamicRegistry\AbstractRegistry;
use Celestriode\DynamicRegistry\DynamicPopulatorInterface;
use Celestriode\DynamicRegistry\Exception\InvalidValue;

/**
 * Sample populator using values from Minecraft: Java Edition version 1.17.1. Avoid using this; it is purely an example.
 *
 * @package Celestriode\ConstructuresMinecraft\Utils\Populators
 */
class TranslationsPopulator implements DynamicPopulatorInterface
{
    /**
     * @inheritDoc
     * @throws InvalidValue
     */
    public function populate(AbstractRegistry $registry): void
    {
        $data = json_decode(file_get_contents(__DIR__ . '/data/translations.json'));

        $registry->addValues(...$data);
    }
}