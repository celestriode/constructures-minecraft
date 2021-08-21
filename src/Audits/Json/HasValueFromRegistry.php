<?php /** @noinspection PhpMissingParentConstructorInspection */

namespace Celestriode\ConstructuresMinecraft\Audits\Json;

use Celestriode\Constructure\AbstractConstructure;
use Celestriode\ConstructuresMinecraft\Registries\AbstractRegistry;
use Celestriode\JsonConstructure\Context\Audits\HasValue;
use Celestriode\JsonConstructure\Structures\Types\AbstractJsonPrimitive;

/**
 * Determines whether or not an input's value exists within a registry. If the audit is set to lenient, a value not in
 * a registry will trigger an event but still return true.
 *
 * @package Celestriode\ConstructuresMinecraft\Audits\Json
 */
class HasValueFromRegistry extends HasValue
{
    public const INVALID_VALUE = 'c3e8dfd3-7e15-4903-8378-6e14b66e4ef1';
    public const CUSTOM_VALUE = 'f03e85f3-0919-49f1-9f68-be3d66be1945';

    /**
     * @var AbstractRegistry The registry to check values from.
     */
    protected $registry;

    /**
     * @var bool Whether or not custom values are allowed.
     */
    private $lenient;

    public function __construct(AbstractRegistry $registry, bool $lenient = false)
    {
        $this->registry = $registry;
        $this->lenient = $lenient;
    }

    /**
     * Returns whether or not custom values are allowed. Will still fire an event, but the audit will pass instead.
     *
     * @return bool
     */
    public function isLenient(): bool
    {
        return $this->lenient;
    }

    /**
     * Return the registry associated with this audit.
     *
     * @return AbstractRegistry
     */
    public function getRegistry(): AbstractRegistry
    {
        return $this->registry;
    }

    /**
     * @inheritDoc
     */
    public function auditPrimitive(AbstractConstructure $constructure, AbstractJsonPrimitive $input, AbstractJsonPrimitive $expected): bool
    {
        // Set values to that of the registry's values.

        $this->values = $this->getRegistry()->populate()->getValues(); // TODO: memory issue putting it into $->values?

        // Validate the value.

        $result = parent::auditPrimitive($constructure, $input, $expected);

        // If the input didn't exist in the registry...

        if (!$result) {

            // If the audit is not lenient, trigger event and return false.

            if (!$this->isLenient()) {

                $constructure->getEventHandler()->trigger(self::INVALID_VALUE, $this, $input, $expected);

                return false;
            }

            // If the audit is lenient, return true. This means custom values are allowed. Trigger a different event.

            $constructure->getEventHandler()->trigger(self::CUSTOM_VALUE, $this, $input, $expected);

            return true;
        }

        // Input was in registry, audit passes.

        return true;
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return 'has_value_from_registry';
    }
}