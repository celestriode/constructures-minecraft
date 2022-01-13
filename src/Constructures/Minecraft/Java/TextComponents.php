<?php namespace Celestriode\ConstructuresMinecraft\Constructures\Minecraft\Java;

use Celestriode\Constructure\Context\AuditInterface;
use Celestriode\Constructure\Context\Audits\BitwiseAudits;
use Celestriode\Constructure\Structures\StructureInterface;
use Celestriode\ConstructuresMinecraft\Audits\General\CoordinateSetTrait;
use Celestriode\ConstructuresMinecraft\Audits\Json\CoordinateSet;
use Celestriode\ConstructuresMinecraft\Audits\Json\HasValueFromRegistry;
use Celestriode\ConstructuresMinecraft\Audits\Json\HasResourceFromRegistry;
use Celestriode\ConstructuresMinecraft\Audits\Json\IsInteger;
use Celestriode\ConstructuresMinecraft\Audits\Json\IsResourceLocation;
use Celestriode\ConstructuresMinecraft\Audits\Json\ValidColor;
use Celestriode\ConstructuresMinecraft\Audits\Json\ValidFile;
use Celestriode\ConstructuresMinecraft\Audits\Json\ValidNbtPath;
use Celestriode\ConstructuresMinecraft\Audits\Json\ValidSelector;
use Celestriode\ConstructuresMinecraft\Audits\Json\ValidSnbt;
use Celestriode\ConstructuresMinecraft\Audits\Json\ValidUrl;
use Celestriode\ConstructuresMinecraft\Audits\Json\ValidUuid;
use Celestriode\ConstructuresMinecraft\Constructures\ConstructuresInterface;
use Celestriode\ConstructuresMinecraft\Utils\EnumEdition;
use Celestriode\DynamicMinecraftRegistries\Java\Game\Registries\EntityTypes;
use Celestriode\DynamicMinecraftRegistries\Java\Game\Registries\Items;
use Celestriode\DynamicMinecraftRegistries\Java\Resources\Fonts;
use Celestriode\DynamicMinecraftRegistries\Java\Resources\Keybinds;
use Celestriode\DynamicMinecraftRegistries\Java\Resources\Translations;
use Celestriode\DynamicRegistry\Exception\InvalidValue;
use Celestriode\JsonConstructure\Context\Audits\Branch;
use Celestriode\JsonConstructure\Context\Audits\ChildHasValue;
use Celestriode\JsonConstructure\Context\Audits\ExclusiveFields;
use Celestriode\JsonConstructure\Context\Audits\HasValue;
use Celestriode\JsonConstructure\Context\Audits\InclusiveFields;
use Celestriode\JsonConstructure\Context\Audits\NumberRange;
use Celestriode\JsonConstructure\Context\Audits\StringLength;
use Celestriode\JsonConstructure\Utils\Json;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Expected structure for text components. Use getStructure() for the whole thing.
 *
 * @package Celestriode\ConstructuresMinecraft\Constructures\Minecraft\Java
 */
class TextComponents implements ConstructuresInterface
{
    /**
     * The raw UUID of the entire text component structure, necessary for nested recursion.
     */
    protected const UUID = '8d5cdf42-a99c-4774-a38f-5b26214b608e';

    /**
     * @var UuidInterface The UUID of the structure.
     */
    private static $uuid;

    /**
     * @inheritDoc
     * @throws InvalidValue
     */
    public static function getStructure(): StructureInterface
    {
        // Start with primitive. Not nullable.

        $structure = Json::primitive()->setUUID(self::getUUID());

        // Add recursion via array.

        $structure->addType(Json::array()->addElement(Json::redirect(self::getUUID())));
        $selector = new ValidSelector(EnumEdition::JAVA);

        // Prepare the bulk.

        $component = Json::object()->failOnUnexpectedKeys(true)->addAudits(static::getTextAudit(), static::getSeparatorBranch(), static::getTranslationBranch(), ...static::getNbtBranches())
            ->addChild('text', Json::string())
            ->addChild('selector', Json::string()->addAudit($selector))
            ->addChild('translate', Json::string()->addAudit(static::getTranslationAudit()))
            ->addChild('score', Json::object()
                ->addChild('name', Json::string()->required()->addAudit(new BitwiseAudits(BitwiseAudits::OR, $selector, new HasValue('*'), new StringLength(1, 40))))
                ->addChild('objective', Json::string()->required()->addAudit(new StringLength(1, 16))))
            ->addChild('keybind', Json::string()->addAudit(static::getKeybindAudit()))
            ->addChild('nbt', Json::string()->addAudit(static::getNbtPathAudit()))
            ->addChild('extra', Json::array()
                ->addElement(Json::redirect(self::getUUID())));

        // Include style.

        $component->addChild('bold', Json::boolean())
            ->addChild('italic', Json::boolean())
            ->addChild('underlined', Json::boolean())
            ->addChild('strikethrough', Json::boolean())
            ->addChild('obfuscated', Json::boolean())
            ->addChild('color', Json::string()->addAudit(static::getColorAudit()))
            ->addChild('insertion', Json::string())
            ->addChild('font', Json::string()->addAudit(static::getFontAudit()));

        // Include clickEvent.

        $component->addChild('clickEvent', Json::object()->addAudits(...static::getClickEventBranches())
            ->addChild('action', Json::string()->required()));

        // Include hoverEvent

        $component->addChild('hoverEvent', Json::object()->addAudits(...static::getHoverEventBranches())
            ->addChild('action', Json::string()->required()));

        // Add the bulk.
        /**
        "hoverEvent" {"action" + "contents" + "value"} (complex, look into)
         */

        $structure->addType($component);

        // Return the completed structure.

        return $structure;
    }

    /**
     * @inheritDoc
     */
    final public static function getUUID(): UuidInterface
    {
        if (static::$uuid === null) {

            static::$uuid = Uuid::fromString(static::UUID);
        }

        return static::$uuid;
    }

    /**
     * Returns audit that ensures some text to display is provided, and only one of such is given.
     *
     * @return AuditInterface
     */
    protected static function getTextAudit(): AuditInterface
    {
        return (new ExclusiveFields('text', 'selector', 'translate', 'score', 'nbt', 'keybind'))->required();
    }

    /**
     * Returns the branch for the "separator", used alongside selectors. Recursive text component.
     *
     * @return AuditInterface
     */
    protected static function getSeparatorBranch(): AuditInterface
    {
        $branch = Json::object()
            ->addChild('separator', Json::redirect(self::getUUID()));

        return new Branch('selector+separator', $branch, new InclusiveFields('selector'));
    }

    /**
     * Returns the branch for translations, specifically to inject "with".
     *
     * @return AuditInterface
     */
    protected static function getTranslationBranch(): AuditInterface
    {
        $branch = Json::object()
            ->addChild('with', Json::array()
                ->addElement(Json::redirect(self::getUUID())));

        return new Branch('translate', $branch, new InclusiveFields('translate'));
    }

    /**
     * Returns all branches for the "nbt" string, as well as the audit that ensures they are used correctly.
     *
     * @return array
     */
    protected static function getNbtBranches(): array
    {
        $branches = [];

        // "block" branch.

        $branch1 = Json::object()
            ->addChild('interpret', Json::boolean())
            ->addChild('separator', Json::redirect(self::getUUID()))
            ->addChild('block', Json::string()->addAudit(static::getBlockAudit()));

        // "entity" branch.

        $branch2 = Json::object()
            ->addChild('interpret', Json::boolean())
            ->addChild('separator', Json::redirect(self::getUUID()))
            ->addChild('entity', Json::string()->addAudit(ValidSelector::get()));

        // "storage" branch.

        $branch3 = Json::object()
            ->addChild('interpret', Json::boolean())
            ->addChild('separator', Json::redirect(self::getUUID()))
            ->addChild('storage', Json::string()->addAudit(IsResourceLocation::get()));

        $branches[] = new Branch('nbt.block', $branch1, new InclusiveFields('nbt', 'block'));
        $branches[] = new Branch('nbt.entity', $branch2, new InclusiveFields('nbt', 'entity'));
        $branches[] = new Branch('nbt.storage', $branch3, new InclusiveFields('nbt', 'storage'));

        // Can only have one of those, and one must exist when "nbt" does.

        $branches[] = (new ExclusiveFields('block', 'entity', 'storage'))->required()->addPredicate(new InclusiveFields('nbt'));

        return $branches;
    }

    /**
     * Returns audit verifying color inputs (including hex value).
     *
     * @return AuditInterface
     */
    protected static function getColorAudit(): AuditInterface
    {
        return new ValidColor(ValidColor::HEX_WITH_PREFIX | ValidColor::NAME);
    }

    /**
     * Returns audit verifying font inputs.
     *
     * @return AuditInterface
     * @throws InvalidValue
     */
    protected static function getFontAudit(): AuditInterface
    {
        return new HasResourceFromRegistry(Fonts::get(), true);
    }

    /**
     * Returns audit verifying keybind inputs.
     *
     * @return AuditInterface
     * @throws InvalidValue
     */
    protected static function getKeybindAudit(): AuditInterface
    {
        return new HasValueFromRegistry(Keybinds::get());
    }

    /**
     * Returns audit verifying translation keys.
     *
     * @return AuditInterface
     * @throws InvalidValue
     */
    protected static function getTranslationAudit(): AuditInterface
    {
        return new HasValueFromRegistry(Translations::get(), true);
    }

    /**
     * Returns audit verifying coordinate input.
     *
     * @return AuditInterface
     */
    protected static function getBlockAudit(): AuditInterface
    {
        return new CoordinateSet(CoordinateSetTrait::$ABSOLUTE | CoordinateSetTrait::$RELATIVE | CoordinateSetTrait::$LOCAL);
    }

    /**
     * Returns audit specific to NBT paths.
     *
     * TODO: cross-validate NBT path with entity NBT. Big.
     *
     * @return AuditInterface
     */
    protected static function getNbtPathAudit(): AuditInterface
    {
        return ValidNbtPath::get();
    }

    /**
     * Returns all click event branches, as well as the audit that ensures the action is restricted to these events.
     *
     * @return array
     */
    protected static function getClickEventBranches(): array
    {
        $branches = [];

        $openUrl = Json::object()
            ->addChild('value', Json::string()->required()->addAudit(ValidUrl::get()));

        $openFile = Json::object()
            ->addChild('value', Json::string()->required()->addAudit(ValidFile::get()));

        $runCommand = Json::object()
            ->addChild('value', Json::string()->required()); // TODO major audit for parsing commands. Big.

        $suggestCommand = Json::object()
            ->addChild('value', Json::string()->required());

        $changePage = Json::object()
            ->addChild('value', Json::string()->required()->addAudit(IsInteger::get()));

        $copyToClipboard = Json::object()
            ->addChild('value', Json::string()->required());

        $branches[] = new Branch('open_url', $openUrl, new ChildHasValue('action', 'open_url'));
        $branches[] = new Branch('open_file', $openFile, new ChildHasValue('action', 'open_file'));
        $branches[] = new Branch('run_command', $runCommand, new ChildHasValue('action', 'run_command'));
        $branches[] = new Branch('suggest_command', $suggestCommand, new ChildHasValue('action', 'suggest_command'));
        $branches[] = new Branch('change_page', $changePage, new ChildHasValue('action', 'change_page'));
        $branches[] = new Branch('copy_to_clipboard', $copyToClipboard, new ChildHasValue('action', 'copy_to_clipboard'));

        $branches[] = new ChildHasValue('action', 'open_url', 'open_file', 'run_command', 'suggest_command', 'change_page', 'copy_to_clipboard');

        return $branches;
    }

    /**
     * Returns all hover event branches, as well as the audit that ensures the action is restricted to these events.
     *
     * @return array
     * @throws InvalidValue
     */
    protected static function getHoverEventBranches(): array
    {
        $branches = [];

        $showText = Json::object()
            ->addChild('contents', Json::redirect(self::getUUID())->required());

        $showItem = Json::object()
            ->addChild('contents', Json::object()->required()
                ->addChild('id', Json::string()->required()->addAudit(new HasResourceFromRegistry(Items::get())))
                ->addChild('count', Json::integer()->addAudit(new NumberRange(0)))
                ->addChild('tag', Json::string()->addAudit(ValidSnbt::get()))); // TODO: NBT constructure. Big.

        $showEntity = Json::object()
            ->addChild('contents', Json::object()->required()
                ->addChild('type', Json::string()->required()->addAudit(new HasResourceFromRegistry(EntityTypes::get())))
                ->addChild('id', Json::string()->required()->addAudit(ValidUuid::get()))
                ->addChild('name', Json::redirect(self::getUUID())));

        $branches[] = new Branch('show_text', $showText, new ChildHasValue('action', 'show_text'));
        $branches[] = new Branch('show_item', $showItem, new ChildHasValue('action', 'show_item'));
        $branches[] = new Branch('show_entity', $showEntity, new ChildHasValue('action', 'show_entity'));

        $branches[] = new ChildHasValue('action', 'show_text', 'show_item', 'show_entity');

        return $branches;
    }
}
