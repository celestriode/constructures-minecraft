<?php namespace Celestriode\ConstructuresMinecraft\Registries\Java\Resources;

use Celestriode\DynamicRegistry\AbstractStringRegistry;

class Entities extends AbstractStringRegistry
{
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'entity_names';
    }
}