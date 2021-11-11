<?php namespace Celestriode\ConstructuresMinecraft\Audits\Json;

use Celestriode\Constructure\AbstractConstructure;
use Celestriode\ConstructuresMinecraft\Audits\MinecraftAuditTrait;
use Celestriode\ConstructuresMinecraft\Constructures\Minecraft\Java\TargetSelectors as JavaTargetSelectors;
use Celestriode\ConstructuresMinecraft\Constructures\Minecraft\Bedrock\TargetSelectors as BedrockTargetSelectors;
use Celestriode\ConstructuresMinecraft\Utils\EnumEdition;
use Celestriode\DynamicRegistry\Exception\InvalidValue;
use Celestriode\JsonConstructure\Context\Audits\AbstractStringAudit;
use Celestriode\JsonConstructure\Structures\Types\JsonString;
use Celestriode\TargetSelectorConstructure\Exceptions\ConversionFailure;

/**
 * Parses a target selector. Does not (TODO) validate if selector works in context.
 *
 * @package Celestriode\ConstructuresMinecraft\Audits\Json
 */
class ValidSelector extends AbstractStringAudit
{
    use MinecraftAuditTrait;

    public const INVALID_SYNTAX = '26a7952f-56a7-4375-84de-99b6d44c3b1e';

    public function __construct(int $edition = EnumEdition::JAVA)
    {
        $this->setEdition($edition);
    }

    /**
     * @inheritDoc
     * @throws InvalidValue
     */
    protected function auditString(AbstractConstructure $constructure, JsonString $input, JsonString $expected): bool
    {
        $raw = $input->getString();

        // Attempt to parse the selector based on the edition.

        try {

            switch ($this->getEdition()) {

                case EnumEdition::BEDROCK:
                    return $this->validateBedrockSelector($raw, $constructure, $input, $expected);
                default:
                    return $this->validateJavaSelector($raw, $constructure, $input, $expected);
            }
        } catch (ConversionFailure $e) {

            // Selector parsing failed, trigger event and return false.

            $constructure->getEventHandler()->trigger(self::INVALID_SYNTAX, $e, $this, $input, $expected);

            return false;
        }
    }

    /**
     * @throws InvalidValue
     * @throws ConversionFailure
     */
    protected function validateJavaSelector(string $raw, AbstractConstructure $constructure, JsonString $input, JsonString $expected): bool
    {
        $selectorExpected = JavaTargetSelectors::getStructure();
        $selectorConstructure = JavaTargetSelectors::getConstructure($constructure->getEventHandler());
        $selectorInput = $selectorConstructure->toStructure($raw);

        if (!$selectorConstructure->validate($selectorInput, $selectorExpected)) {

            return false;
        }

        return true;
    }

    /**
     * @throws ConversionFailure
     * @throws InvalidValue
     */
    protected function validateBedrockSelector(string $raw, AbstractConstructure $constructure, JsonString $input, JsonString $expected): bool
    {
        $selectorExpected = BedrockTargetSelectors::getStructure();
        $selectorConstructure = BedrockTargetSelectors::getConstructure($constructure->getEventHandler());
        $selectorInput = $selectorConstructure->toStructure($raw);

        if (!$selectorConstructure->validate($selectorInput, $selectorExpected)) {

            return false;
        }

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