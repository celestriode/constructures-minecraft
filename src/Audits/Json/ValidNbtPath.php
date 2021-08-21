<?php namespace Celestriode\ConstructuresMinecraft\Audits\Json;

use Celestriode\Captain\Exceptions\CommandSyntaxException;
use Celestriode\Constructure\AbstractConstructure;
use Celestriode\JsonConstructure\Context\Audits\AbstractStringAudit;
use Celestriode\JsonConstructure\Structures\Types\JsonString;
use Celestriode\Mattock\Exceptions\MattockException;
use Celestriode\Mattock\Parsers\Java\NbtPathParser;

/**
 * Validates the syntax for an NBT path string. Does not (TODO) validate if the path can exist.
 *
 * @package Celestriode\ConstructuresMinecraft\Audits\Json
 */
class ValidNbtPath extends AbstractStringAudit
{
    public const INVALID_SYNTAX = '78690f3c-a9aa-4563-a793-b27c66429400';

    /**
     * @inheritDoc
     */
    protected function auditString(AbstractConstructure $constructure, JsonString $input, JsonString $expected): bool
    {
        try {

            NbtPathParser::parse($input->getString());

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
        return 'valid_nbt_path';
    }
}