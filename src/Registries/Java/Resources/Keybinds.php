<?php namespace Celestriode\ConstructuresMinecraft\Registries\Java\Resources;

use Celestriode\DynamicRegistry\AbstractStringRegistry;

class Keybinds extends AbstractStringRegistry
{
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'keybind_keys';
    }
}