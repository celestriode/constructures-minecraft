<?php namespace Celestriode\ConstructuresMinecraft\Registries\Resources;

use Celestriode\ConstructuresMinecraft\Registries\AbstractRegistry;

class Translations extends AbstractRegistry
{
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'translation_keys';
    }
}