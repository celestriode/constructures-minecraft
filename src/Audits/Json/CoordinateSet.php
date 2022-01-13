<?php namespace Celestriode\ConstructuresMinecraft\Audits\Json;

use Celestriode\Constructure\AbstractConstructure;
use Celestriode\ConstructuresMinecraft\Audits\General\CoordinateSetTrait;
use Celestriode\JsonConstructure\Context\Audits\AbstractStringAudit;
use Celestriode\JsonConstructure\Structures\Types\JsonString;

/**
 * Validates a set of coordinates that take the form "1 2 3" (that is, each coordinate is separated by a space). The
 * number of expected coordinates in the set can be changed, as well as the type of coordinate to expect (absolute,
 * relative, and/or local).
 *
 * @package Celestriode\ConstructuresMinecraft\Audits\Json
 */
class CoordinateSet extends AbstractStringAudit
{
    use CoordinateSetTrait;

    /**
     * @inheritDoc
     */
    protected function auditString(AbstractConstructure $constructure, JsonString $input, JsonString $expected): bool
    {
        return $this->auditCoordinateSet(explode(' ', $input->getString(), $this->getCoordinateCount() + 1), $this, $constructure, $input, $expected);
    }
}