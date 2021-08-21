<?php namespace Celestriode\ConstructuresMinecraft\Audits\Json;

use Celestriode\Constructure\AbstractConstructure;
use Celestriode\JsonConstructure\Context\Audits\AbstractStringAudit;
use Celestriode\JsonConstructure\Structures\Types\JsonString;

/**
 * Determines if the input is a valid file path, specifically for the "open_file" click event for text components.
 *
 * While "open_file" is a valid action, it is not accessible in vanilla, thus a special event should be fired.
 *
 * @package Celestriode\ConstructuresMinecraft\Audits\Json
 */
class ValidFile extends AbstractStringAudit
{
    public const NO_USAGE = '8e58f753-b90c-4c7b-bf0a-40b3b279737b';

    /**
     * @inheritDoc
     */
    protected function auditString(AbstractConstructure $constructure, JsonString $input, JsonString $expected): bool
    {
        // The "open_file" click event is not allowed to be used.

        $constructure->getEventHandler()->trigger(self::NO_USAGE, $this, $input, $expected);

        return false;
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return 'valid_file';
    }
}