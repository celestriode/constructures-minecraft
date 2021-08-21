<?php namespace Celestriode\ConstructuresMinecraft\Audits\Json;

use Celestriode\Captain\Exceptions\CommandSyntaxException;
use Celestriode\Constructure\AbstractConstructure;
use Celestriode\JsonConstructure\Context\Audits\AbstractStringAudit;
use Celestriode\JsonConstructure\Structures\Types\JsonString;
use Celestriode\Mattock\Exceptions\MattockException;
use Celestriode\Mattock\Parsers\Java\StringifiedNbtParser;

/**
 * Parses a stringified NBT input. Does not (TODO) validate if the NBT is acceptable within the context.
 *
 * @package Celestriode\ConstructuresMinecraft\Audits\Json
 */
class ValidSnbt extends AbstractStringAudit
{
    public const INVALID_SYNTAX = 'da2d3d11-bdce-42ea-b108-22940e9cbb9e';

    /**
     * @inheritDoc
     */
    protected function auditString(AbstractConstructure $constructure, JsonString $input, JsonString $expected): bool
    {
        try {

            StringifiedNbtParser::parse($input->getString());

            return true;
        } catch (CommandSyntaxException | MattockException $e) {

            $constructure->getEventHandler()->trigger(self::INVALID_SYNTAX, $e, $this, $input, $expected);

            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return 'valid_snbt';
    }
}