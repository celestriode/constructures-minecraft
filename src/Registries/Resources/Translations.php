<?php namespace Celestriode\ConstructuresMinecraft\Registries\Resources;

use Celestriode\DynamicRegistry\AbstractStringRegistry;

class Translations extends AbstractStringRegistry
{
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'translation_keys';
    }
}