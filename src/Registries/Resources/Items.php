<?php namespace Celestriode\ConstructuresMinecraft\Registries\Resources;

use Celestriode\ConstructuresMinecraft\Registries\AbstractRegistry;

class Items extends AbstractRegistry
{
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'item_names';
    }
}