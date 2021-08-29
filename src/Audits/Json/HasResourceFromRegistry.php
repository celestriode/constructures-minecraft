<?php namespace Celestriode\ConstructuresMinecraft\Audits\Json;

use Celestriode\Captain\Exceptions\CommandSyntaxException;
use Celestriode\Captain\StringReader;
use Celestriode\Constructure\AbstractConstructure;
use Celestriode\ConstructuresMinecraft\Utils\ResourceLocation;
use Celestriode\JsonConstructure\Structures\Types\AbstractJsonPrimitive;

/**
 * Determines whether or not the input is a valid resource location and belongs to a registry. If it does not belong to
 * a registry and the audit is marked as lenient, then the audit will pass but an event is still triggered. In some
 * cases, custom resources can be created, allowing resource locations that are valid outside of the vanilla game.
 *
 * @package Celestriode\ConstructuresMinecraft\Audits\Json
 */
class HasResourceFromRegistry extends HasValueFromRegistry
{
    public const INVALID_SYNTAX = '8be01f1f-c19a-426b-a1fc-db8be35606fb';
    public const CUSTOM_RESOURCE = '32fe8956-ce86-4bcd-98fb-0d9d87b7cc94';
    public const INVALID_RESOURCE = '4c22e47f-4240-4239-8a78-75f9c6ddc9d1';

    /**
     * @inheritDoc
     */
    public function auditPrimitive(AbstractConstructure $constructure, AbstractJsonPrimitive $input, AbstractJsonPrimitive $expected): bool
    {
        // Attempt to parse the input as a resource location.

        try {

            // Obtain the resource.

            $resource = ResourceLocation::read(new StringReader($input->getString()));

        } catch (CommandSyntaxException $e) {

            // If the resource location isn't of valid syntax, the audit fails.

            $constructure->getEventHandler()->trigger(self::INVALID_SYNTAX, $e, $this, $input, $expected);

            return false;
        }

        // Match the resource.

        $found = $resource->validResource($this->getRegistry());

        // If it's not found...

        if (!$found) {

            // And if it's lenient, trigger an event about custom resources and return true.

            if ($this->isLenient()) {

                $constructure->getEventHandler()->trigger(self::CUSTOM_RESOURCE, $resource, $this, $input, $expected);

                return true;
            }

            // Otherwise, trigger an event about invalid resources.

            $constructure->getEventHandler()->trigger(self::INVALID_RESOURCE, $resource, $this, $input, $expected);
        }

        // Return whether or not the resource was found.

        return $found;
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return "has_resource_from_registry";
    }
}