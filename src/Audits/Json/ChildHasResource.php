<?php namespace Celestriode\ConstructuresMinecraft\Audits\Json;

use Celestriode\Captain\Exceptions\CommandSyntaxException;
use Celestriode\Captain\StringReader;
use Celestriode\Constructure\AbstractConstructure;
use Celestriode\ConstructuresMinecraft\Registries\AbstractRegistry;
use Celestriode\ConstructuresMinecraft\Utils\ResourceLocation;
use Celestriode\JsonConstructure\Context\Audits\ChildHasValue;
use Celestriode\JsonConstructure\Structures\Types\JsonObject;
use Celestriode\JsonConstructure\Structures\Types\JsonString;

/**
 * Determines whether or not a child field within an object contains a specific resource location from a registry.
 *
 * @package Celestriode\ConstructuresMinecraft\Audits\Json
 */
class ChildHasResource extends ChildHasValue
{
    public const INVALID_RESOURCE = '4053b75a-fa36-4106-9a3f-86172b2f5c0c';
    public const INVALID_SYNTAX = 'e5932803-9e1e-4e30-bbaf-4a60c0a2f30b';

    /**
     * @var AbstractRegistry The registry to verify values from.
     */
    protected $registry;

    public function __construct(string $key, AbstractRegistry $registry)
    {
        parent::__construct($key);

        $this->registry = $registry;
    }

    /**
     * Returns the registry that contains the values expected for the input.
     *
     * @return AbstractRegistry
     */
    public function getRegistry(): AbstractRegistry
    {
        return $this->registry;
    }

    /**
     * Audits two objects.
     *
     * @param AbstractConstructure $constructure The base constructure object, which holds the event handler.
     * @param JsonObject $input The input to be compared with the expected structure.
     * @param JsonObject $expected The expected structure that the input should adhere to.
     * @return boolean
     */
    protected function auditObject(AbstractConstructure $constructure, JsonObject $input, JsonObject $expected): bool
    {
        // Ensure the child exists and is a string.

        $child = $input->getChild($this->getKey());

        if ($child === null || !($child instanceof JsonString)) {

            $constructure->getEventHandler()->trigger(self::INVALID_CHILD, $this, $input, $expected);

            return false;
        }

        // If the input has the accepted values, return true.

        try {

            // Obtain the resource.

            $resource = ResourceLocation::read(new StringReader($child->getString()));

            // Match the resource.

            if (!$resource->validResource($this->getRegistry())) {

                $constructure->getEventHandler()->trigger(self::INVALID_RESOURCE, $resource, $this, $input, $expected);

                return false;
            }

            return true;

        } catch (CommandSyntaxException $e) {

            // If the resource location isn't of valid syntax, the audit fails.

            $constructure->getEventHandler()->trigger(self::INVALID_SYNTAX, $e, $this, $input, $expected);

            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return "child_has_resource";
    }
}