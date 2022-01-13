<?php namespace Celestriode\ConstructuresMinecraft\Audits\General;

use Celestriode\Constructure\AbstractConstructure;
use Celestriode\Constructure\Context\AuditInterface;
use Celestriode\Constructure\Structures\StructureInterface;

trait CoordinateSetTrait
{

    public static $WRONG_COORDINATE_COUNT = '304217ac-9773-47fd-a7a1-3a2be55c9f91';
    public static $INVALID_COORDINATES = '209c7f01-3c7e-4270-b90d-67d139ed9265';
    public static $MIXED_LOCAL = '8e23087c-f8ed-4139-847f-18602f011dc3';

    /**
     * When included in options, absolute coordinates are allowed.
     */
    public static $ABSOLUTE = 1;

    /**
     * When included in options, relative coordinates are allowed.
     */
    public static $RELATIVE = 2;

    /**
     * When included in options, local coordinates are allowed.
     */
    public static $LOCAL = 4;

    /**
     * @var int The bit field describing options for the audit.
     */
    protected $options;

    /**
     * @var int The number of expected axes in the input coordinate set.
     */
    protected $coordinateCount;

    /**
     * @var int The number of local coordinates the input uses.
     */
    protected $localCount = 0;

    public function __construct(int $options, int $coordinateCount = 3)
    {
        $this->options = $options;
        $this->coordinateCount = $coordinateCount;
    }

    /**
     * Returns the bit field describing the options for the audit (which consist of valid types of coordinates to use).
     *
     * @return int
     */
    public function getOptions(): int
    {
        return $this->options;
    }

    /**
     * Returns how many axes are expected.
     *
     * @return int
     */
    public function getCoordinateCount(): int
    {
        return $this->coordinateCount;
    }

    /**
     * Returns how many local coordinates were used in the input. If a set of coordinates includes a local coordinate,
     * then all values in the coordinate set must be local coordinates.
     *
     * Since this value is set during an audit, it must be set back to 0 once the audit concludes.
     *
     * @return int
     */
    public function getLocalCount(): int
    {
        return $this->localCount;
    }

    protected function auditCoordinateSet(array $set, AuditInterface $audit, AbstractConstructure $constructure, StructureInterface $input, StructureInterface $expected): bool
    {
        // If the number of parts is incorrect, stop the audit.

        if (count($set) != $this->getCoordinateCount()) {

            $constructure->getEventHandler()->trigger(self::$WRONG_COORDINATE_COUNT, $set, $audit, $input, $expected);

            return false;
        }

        // Check each pair for validity.

        $wrongAxes = [];

        foreach ($set as $axis) {

            if (!$this->validAxis($axis)) {

                $wrongAxes[] = $axis;
            }
        }

        // Trigger event if there were any incorrect axes.

        if (!empty($wrongAxes)) {

            $constructure->getEventHandler()->trigger(self::$INVALID_COORDINATES, $wrongAxes, $set, $audit, $input, $expected);
        }

        // If the number of local coordinates encountered did not equal the number of required coordinates, audit fails.

        if ($this->getLocalCount() > 0 && $this->getCoordinateCount() != $this->getLocalCount()) {

            $constructure->getEventHandler()->trigger(self::$MIXED_LOCAL, $set, $audit, $input, $expected);

            $this->localCount = 0; // Audits should be put back to their original position.

            return false;
        }

        $this->localCount = 0; // Audits should be put back to their original position.

        // Return whether or not the validation succeeded.

        return empty($wrongAxes);
    }

    /**
     * Returns whether or not the single coordinate is a valid coordinate, based on the options supplied to the audit.
     * For example, if the audit accepts only absolute coordinates, then this will return false if the coordinate is
     * ~3 or ^3.
     *
     * @param string $axis
     * @return bool
     */
    protected function validAxis(string $axis): bool
    {
        $succeeds = false;

        // Check absolute values.

        if (($this->getOptions() & self::$ABSOLUTE) && is_numeric($axis)) {

            $succeeds = true;
        }

        // Check relative values.

        if (($this->getOptions() & self::$RELATIVE) && ($axis == '~' || (substr($axis, 0, 1) == '~' && is_numeric(substr($axis, 1))))) {

            $succeeds = true;
        }

        // Check local values.

        if (($this->getOptions() & self::$LOCAL) && ($axis == '^' || (substr($axis, 0, 1) == '^' && is_numeric(substr($axis, 1))))) {

            $this->localCount++; // Increase number of local coordinates. Must match coordinate count.
            $succeeds = true;
        }

        // All done, return whether or not it succeeded.

        return $succeeds;
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return 'coordinate_set';
    }

    /**
     * @inheritDoc
     */
    public function toString(): string
    {
        return self::getName() . '{count=' . $this->getCoordinateCount() . ',absolute=' . (($this->getOptions() & self::$ABSOLUTE) ? 'true' : 'false') . ',relative=' . (($this->getOptions() & self::$RELATIVE) ? 'true' : 'false') . ',local=' . (($this->getOptions() & self::$LOCAL) ? 'true' : 'false') . '}';
    }
}