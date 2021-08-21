<?php namespace Celestriode\ConstructuresMinecraft\Audits\Json;

use Celestriode\Constructure\AbstractConstructure;
use Celestriode\JsonConstructure\Context\Audits\AbstractStringAudit;
use Celestriode\JsonConstructure\Structures\Types\JsonString;

/**
 * Determines if an input strictly follows the Minecraft-specific UUID format. This variant is lenient, such that each
 * hex section does not have to fill the entire section (e.g. 1-1-1-1-1 is a valid UUID in Minecraft).
 *
 * @package Celestriode\ConstructuresMinecraft\Audits\Json
 */
class ValidUuid extends AbstractStringAudit
{
    public const INVALID_SYNTAX = '8ab49c1b-a2e0-4d91-8872-b4a38668c4c8';

    public const UUID = '/^[0-9a-f]{1,8}-[0-9a-f]{1,4}-[0-9a-f]{1,4}-[0-9a-f]{1,4}-[0-9a-f]{1,12}$/i';

    /**
     * @inheritDoc
     */
    protected function auditString(AbstractConstructure $constructure, JsonString $input, JsonString $expected): bool
    {
        // Ensure the UUID follows the proper format specifically for Minecraft UUIDs.

        if (!preg_match(self::UUID, $input->getString())) {

            $constructure->getEventHandler()->trigger(self::INVALID_SYNTAX, $this, $input, $expected);

            return false;
        }

        // All good, audit passes.

        return true;
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return 'valid_uuid';
    }
}