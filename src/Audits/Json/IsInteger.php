<?php namespace Celestriode\ConstructuresMinecraft\Audits\Json;

use Celestriode\Constructure\AbstractConstructure;
use Celestriode\JsonConstructure\Context\Audits\AbstractPrimitiveAudit;
use Celestriode\JsonConstructure\Structures\Types\AbstractJsonPrimitive;

/**
 * Determines whether or not the value of the input is an integer. The value may be a string.
 *
 * @package Celestriode\ConstructuresMinecraft\Audits\Json
 */
class IsInteger extends AbstractPrimitiveAudit
{
    public const INVALID_VALUE = '2293b637-3cfe-40e4-913c-5d1d5d888855';

    /**
     * @inheritDoc
     */
    protected function auditPrimitive(AbstractConstructure $constructure, AbstractJsonPrimitive $input, AbstractJsonPrimitive $expected): bool
    {
        // If the input is not specifically an integer (whether string or actually an integer), return false.

        if (filter_var($input->getString(), FILTER_VALIDATE_INT) === false) {

            $constructure->getEventHandler()->trigger(self::INVALID_VALUE, $this, $input, $expected);

            return false;
        }

        // Otherwise the input is an integer.

        return true;
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return 'is_integer';
    }
}