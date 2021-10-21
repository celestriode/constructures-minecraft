<?php namespace Celestriode\ConstructuresMinecraft\Registries\Bedrock\Resources;

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