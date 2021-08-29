<?php namespace Celestriode\ConstructuresMinecraft\Audits\Json;

use Celestriode\Captain\Exceptions\CommandSyntaxException;
use Celestriode\Captain\StringReader;
use Celestriode\Constructure\AbstractConstructure;
use Celestriode\ConstructuresMinecraft\Utils\ResourceLocation;
use Celestriode\JsonConstructure\Context\Audits\AbstractStringAudit;
use Celestriode\JsonConstructure\Structures\Types\JsonString;

/**
 * Determines whether or not the value is a resource location. Does not determine whether or not it matches a value in
 * any registries. Instead, it just checks for syntax.
 *
 * @package Celestriode\ConstructuresMinecraft\Audits\Json
 */
class IsResourceLocation extends AbstractStringAudit
{
    public const INVALID_SYNTAX = '784c6e6c-a291-40a9-9194-96e5d2a01c1a';

    /**
     * @inheritDoc
     */
    protected function auditString(AbstractConstructure $constructure, JsonString $input, JsonString $expected): bool
    {
        // Attempt to parse the input as a resource location.

        try {

            // Obtain the resource.

            ResourceLocation::read(new StringReader($input->getString()));

            return true;

        } catch (CommandSyntaxException $e) {

            // If the resource location isn't of valid syntax, the audit fails.

            $constructure->getEventHandler()->trigger(self::INVALID_SYNTAX, $e, $this, $input, $expected);

            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return 'is_resource_location';
    }
}