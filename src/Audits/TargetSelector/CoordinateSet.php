<?php namespace Celestriode\ConstructuresMinecraft\Audits\TargetSelector;

use Celestriode\Constructure\AbstractConstructure;
use Celestriode\ConstructuresMinecraft\Audits\General\CoordinateSetTrait;
use Celestriode\TargetSelectorConstructure\Context\Audits\AbstractValueAudit;
use Celestriode\TargetSelectorConstructure\Structures\DynamicOptions\Values\AbstractValue;

class CoordinateSet extends AbstractValueAudit
{
    use CoordinateSetTrait;

    /**
     * @inheritDoc
     */
    protected function auditValue(AbstractConstructure $constructure, AbstractValue $input, AbstractValue $expected): bool
    {
        return $this->auditCoordinateSet(explode(' ', $input->getValue(), $this->getCoordinateCount() + 1), $this, $constructure, $input, $expected);
    }
}