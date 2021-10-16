<?php namespace Celestriode\ConstructuresMinecraft\Registries\Resources;

use Celestriode\DynamicRegistry\AbstractStringRegistry;

class Fonts extends AbstractStringRegistry
{
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'font_names';
    }
}