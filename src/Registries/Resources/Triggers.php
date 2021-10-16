<?php namespace Celestriode\ConstructuresMinecraft\Registries\Resources;

use Celestriode\DynamicRegistry\AbstractStringRegistry;

class Triggers extends AbstractStringRegistry
{
    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return 'advancement_trigger_names';
    }
}