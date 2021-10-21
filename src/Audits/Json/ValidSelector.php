<?php namespace Celestriode\ConstructuresMinecraft\Audits\Json;

use Celestriode\Captain\Exceptions\CommandSyntaxException;
use Celestriode\Captain\StringReader;
use Celestriode\Constructure\AbstractConstructure;
use Celestriode\ConstructuresMinecraft\Audits\MinecraftAuditTrait;
use Celestriode\ConstructuresMinecraft\Utils\EnumEdition;
use Celestriode\JsonConstructure\Context\Audits\AbstractStringAudit;
use Celestriode\JsonConstructure\Structures\Types\JsonString;
use Celestriode\Mattock\Exceptions\MattockException;
use Celestriode\Mattock\Exceptions\NotInRegistryException;
use Celestriode\Mattock\Parsers\Java\EntitySelectorParser;

/**
 * Parses a target selector. Does not (TODO) validate if selector works in context.
 *
 * @package Celestriode\ConstructuresMinecraft\Audits\Json
 */
class ValidSelector extends AbstractStringAudit
{
    use MinecraftAuditTrait;

    public const INVALID_SYNTAX = '26a7952f-56a7-4375-84de-99b6d44c3b1e';
    public const INVALID_SUBVALUE = 'bb86dd72-64c4-48e5-9ac8-b1cd250ba817';

    public function __construct(int $edition = EnumEdition::JAVA)
    {
        $this->setEdition($edition);
    }

    /**
     * @inheritDoc
     */
    protected function auditString(AbstractConstructure $constructure, JsonString $input, JsonString $expected): bool
    {
        $raw = $input->getString();

        // Attempt to parse the selector based on the edition.

        try {

            switch ($this->getEdition()) {

                case EnumEdition::JAVA:
                    $parser = new EntitySelectorParser(new StringReader($raw), true);
                    break;
                case EnumEdition::BEDROCK:
                    $parser = new EntitySelectorParser(new StringReader($raw), true); // TODO: use bedrock.
                    break;
                default:
                    $parser = new EntitySelectorParser(new StringReader($raw), true);
            }

            $parser->parse();
        } catch (CommandSyntaxException | MattockException $e) {

            // Selector parsing failed, trigger event and return false.

            if ($e instanceof CommandSyntaxException && $e->getType() instanceof NotInRegistryException) {

                $constructure->getEventHandler()->trigger(self::INVALID_SUBVALUE, $e, $e->getType(), $this, $input, $expected);
            } else {

                $constructure->getEventHandler()->trigger(self::INVALID_SYNTAX, $e, $this, $input, $expected);
            }

            return false;
        }

        // No issues, audit passes.

        return true;
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return 'valid_selector';
    }

    /**
     * Returns the name of the audit. Any other implementation is up to extending libraries.
     *
     * @return string
     */
    public function toString(): string
    {
        return static::getName() . '{' . $this->getEditionString() . '}';
    }
}