<?php namespace Celestriode\ConstructuresMinecraft\Audits\Json;

use Celestriode\Constructure\AbstractConstructure;
use Celestriode\ConstructuresMinecraft\Registries\Java\Resources\Colors;
use Celestriode\DynamicRegistry\Exception\InvalidValue;
use Celestriode\JsonConstructure\Context\Audits\AbstractPrimitiveAudit;
use Celestriode\JsonConstructure\Structures\Types\AbstractJsonPrimitive;

/**
 * Determines whether a value is a valid color based on a variety of possible color formats. These formats range from
 * integer values, hex values, hex values with a # prefix, and one of the 16 literal color names. Multiple formats can
 * be accepted for a value.
 *
 * For example, instantiate the audit by supplying an option value of INTEGER | HEX to allow either to be accepted.
 *
 * @package Celestriode\ConstructuresMinecraft\Audits\Json
 */
class ValidColor extends AbstractPrimitiveAudit
{
    public const INVALID_INTEGER = '62c1dd1f-9d8c-4917-af2f-69607002bd52';
    public const INVALID_HEX = 'a1f712ba-e117-47a9-9fd8-2864937699f9';
    public const INVALID_PREFIX = '0c118241-45e6-4afa-9d33-565425427fa3';
    public const INVALID_COLOR = '864d6e10-545e-456d-ac8d-9be6660b4718';

    /**
     * The prefix of hex colors that require a prefix (e.g. #333333).
     */
    public const PREFIX = '#';

    /**
     * Set this bit to allow integer color values.
     */
    public const INTEGER = 1;

    /**
     * Set this bit to allow hex color values.
     */
    public const HEX = 2;

    /**
     * Set this bit to allow hex color values with a required prefix.
     */
    public const HEX_WITH_PREFIX = 4;

    /**
     * Set this bit to allow color values based on the name of the color.
     */
    public const NAME = 8;

    /**
     * @var int A bit field that states which of the options to use when validating.
     */
    protected $options;

    public function __construct(int $options)
    {
        $this->options = $options;
    }

    /**
     * @inheritDoc
     * @throws InvalidValue
     */
    protected function auditPrimitive(AbstractConstructure $constructure, AbstractJsonPrimitive $input, AbstractJsonPrimitive $expected): bool
    {
        // Halt capturing as there are several paths that can be taken here.

        $succeeds = false;
        $constructure->getEventHandler()->capture();

        // Validate integer.

        if ($this->options & self::INTEGER) {

            // If the input isn't an integer between 0 and 16777215, it's not a valid color integer. ctype_digit doesn't work with negatives, but that's not a concern here.

            if ($input->getInteger() < 0 || $input->getInteger() > 16777215 || !ctype_digit($input->getString())) {

                // Color integer must be an integer.

                $constructure->getEventHandler()->trigger(self::INVALID_INTEGER, $this, $input, $expected);
            } else {

                $succeeds = true;
            }
        }

        // Validate hex.

        if ($this->options & self::HEX) {

            // Assume the string is the hex value with no prefix.

            $hex = $input->getString();

            // Validate the hex value.

            if ($this->validateHex($hex)) {

                $succeeds = true;
            } else {

                $constructure->getEventHandler()->trigger(self::INVALID_HEX, $this, $input, $expected);
            }
        }

        // Validate hex with prefix.

        if ($this->options & self::HEX_WITH_PREFIX) {

            // The hex value must start with the prefix.

            if (substr($input->getString(), 0, 1) === self::PREFIX) {

                // Get the hex value without the prefix.

                $hex = substr($input->getString(), 1);

                // Validate the hex value.

                if ($this->validateHex($hex)) {

                    $succeeds = true;
                } else {

                    $constructure->getEventHandler()->trigger(self::INVALID_HEX, $this, $input, $expected);
                }
            } else {

                // Otherwise the prefix is incorrect.

                $constructure->getEventHandler()->trigger(self::INVALID_PREFIX, $this, $input, $expected);
            }
        }

        // Validate name.

        if ($this->options & self::NAME) {

            // Check the Colors registry for a matching legacy color name.

            if (Colors::get()->has($input->getString())) {

                $succeeds = true;
            } else {

                // If an invalid color name, add an event to the captured list.

                $constructure->getEventHandler()->trigger(self::INVALID_COLOR, $this, $input, $expected);
            }
        }

        // If it succeeded, clear the captured events and return true.

        if ($succeeds) {

            $constructure->getEventHandler()->clear();

            return true;
        }

        // If it fails, then release the captured events to alert the user to the paths they can take.

        $constructure->getEventHandler()->release();

        return false;
    }

    /**
     * Validates an input string to see if it is a valid hex value. Triggers some events if not.
     *
     * Returns true if it's valid or if it's not exactly 6 characters. In Minecraft, a hex value can be any character
     * length, so the user might be inputting an odd value.
     *
     * @param string $hex
     * @return bool
     */
    protected function validateHex(string $hex): bool
    {
        // If it's not a proper hex value at all, the audit fails.

        if (!ctype_xdigit($hex)) {

            return false;
        }

        // If the string length is not 6, then warn the user. While the game doesn't care about this, it is of poor form
        // and so the audit should fail.

        if (strlen($hex) != 6) {

            return false;
        }

        // Otherwise it's fine, return true.

        return true;
    }

    /**
     * Returns the bit field that states which colors can be used with this audit.
     *
     * @return int
     */
    public function getOptions(): int
    {
        return $this->options;
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return 'valid_color';
    }

    /**
     * @inheritDoc
     */
    public function toString(): string
    {
        return self::getName() . '{integer=' . (($this->getOptions() & self::INTEGER) ? 'true' : 'false') . ',hex=' . (($this->getOptions() & self::HEX) ? 'true' : 'false') . ',hex_with_prefix=' . (($this->getOptions() & self::HEX_WITH_PREFIX) ? 'true' : 'false') . ',name=' . (($this->getOptions() & self::NAME) ? 'true' : 'false') . '}';
    }
}