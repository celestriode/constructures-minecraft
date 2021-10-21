<?php namespace Celestriode\ConstructuresMinecraft\Registries\Java\Resources;

use Celestriode\DynamicRegistry\AbstractStringRegistry;

class Items extends AbstractStringRegistry
{
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'item_names';
    }
}