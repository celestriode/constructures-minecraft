<?php namespace Celestriode\ConstructuresMinecraft\Registries\Java\Resources;

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