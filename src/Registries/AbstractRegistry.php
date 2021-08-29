<?php namespace Celestriode\ConstructuresMinecraft\Registries;

use Celestriode\ConstructuresMinecraft\Utils\RegistryPopulator;

/**
 * A registry contains a list of values to be used with various audits that rely on them. For example, if a string input
 * must be one from a list of valid Minecraft blocks from 1.17, then you could use a registry containing those values.
 *
 * Rather than having a registry for each version or manually populating each type of registry, you can use a dynamic
 * registry populator. See DynamicPopulatorInterface for more details. Dynamic populating is what separates registries
 * from a simple HasValue audit; use HasValue for inputs that are not expected to change.
 *
 * @package Celestriode\ConstructuresMinecraft\Registries
 */
abstract class AbstractRegistry
{
    /**
     * @var array Instances of registries.
     */
    private static $instances = [];

    /**
     * @var array The list of values associated with this registry. Use dynamic populators to fill it.
     */
    protected $values = [];

    /**
     * @var bool Whether or not $values has been populated dynamically.
     */
    private $populated = false;

    /**
     * Returns a friendly name of the registry.
     *
     * @return string
     */
    abstract public function getName(): string;

    /**
     * Optionally instantiates with a list of values, overwriting any values already within $values by default.
     *
     * @param mixed ...$values The values to add to the registry.
     */
    protected function __construct(...$values)
    {
        if (!empty($values)) {

            $this->setValues(...$values);
        }
    }

    /**
     * Returns whether or not the list of values has been populated via the populate() method.
     *
     * @return bool
     */
    public function populated(): bool
    {
        return $this->populated;
    }

    /**
     * Resets the registry and then adds the given values to it.
     *
     * @param mixed ...$values The values to add to the registry.
     * @return $this
     */
    public function setValues(...$values): self
    {
        $this->values = [];
        $this->addValues($values);

        return $this;
    }

    /**
     * Adds multiple values to the registry. Any duplicates will be ignored.
     *
     * @param mixed ...$values The values to add to the registry.
     * @return $this
     */
    public function addValues(...$values): self
    {
        foreach ($values as $value) {

            $this->addValue($value);
        }

        return $this;
    }

    /**
     * Adds a value to the registry provided that it is valid.
     *
     * @param mixed $value The value to add to the registry.
     * @return $this
     */
    public function addValue($value): self
    {
        if ($this->validValue($value)) {

            $this->values[] = $value;
        }

        return $this;
    }

    /**
     * Returns whether or not the input value is valid for adding to the registry.
     *
     * The only condition by default is that the value must not already exist in the registry.
     *
     * @param $value
     * @return bool
     */
    protected function validValue($value): bool
    {
        if (in_array($value, $this->getValues())) {

            return false;
        }

        return true;
    }

    /**
     * Returns all the values of the registry. If the registry has no default values and hasn't been populated in some
     * form, the array will be empty. Call populate() first or check populated().
     *
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * Returns whether or not the given value exists within the registry. Attempts to populate the registry if it wasn't
     * already populated.
     *
     * Make sure you populate registries using the RegistryPopulator before calling this method.
     *
     * @param $value
     * @return bool
     */
    public function matches($value): bool
    {
        // Dynamically populate the registry before attempting a match, if it wasn't already populated.
        // Return whether or not the input is within the populated registry.

        return in_array($value,  $this->populate()->getValues());
    }

    /**
     * Populates the registry from dynamic populators if the registry wasn't already populated.
     *
     * @return $this
     */
    final public function populate(): self
    {
        if (!$this->populated()) {

            RegistryPopulator::populateRegistryDynamically($this);

            // Mark the registry as having been populated.

            $this->populated = true;
        }

        return $this;
    }

    /**
     * Creates a singleton of the registry class.
     *
     * @return static
     */
    final public static function get(...$values): self
    {
        // Obtain the singleton.

        $class = self::$instances[static::class] ?? new static(...$values);

        // Store the class if it wasn't already stored.

        if (!isset(self::$instances[static::class])) {

            self::$instances[static::class] = $class;
        }

        // All set, return the class.

        return $class;
    }
}