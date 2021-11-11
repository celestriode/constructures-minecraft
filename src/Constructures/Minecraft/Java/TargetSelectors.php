<?php namespace Celestriode\ConstructuresMinecraft\Constructures\Minecraft\Java;

use Celestriode\Constructure\Context\Audits\AlwaysTrue;
use Celestriode\Constructure\Context\Events\EventHandler;
use Celestriode\Constructure\Context\Events\EventHandlerInterface;
use Celestriode\Constructure\Structures\StructureInterface;
use Celestriode\ConstructuresMinecraft\Constructures\ConstructuresInterface;
use Celestriode\DynamicMinecraftRegistries\Java\Data\Advancements;
use Celestriode\DynamicMinecraftRegistries\Java\Data\Tags;
use Celestriode\DynamicMinecraftRegistries\Java\Game\Gamemodes;
use Celestriode\DynamicMinecraftRegistries\Java\Game\Registries\EntityTypes;
use Celestriode\DynamicMinecraftRegistries\Java\Other\SelectorSorts;
use Celestriode\DynamicMinecraftRegistries\Java\Other\SelectorTargets;
use Celestriode\DynamicRegistry\Exception\InvalidValue;
use Celestriode\Mattock\Parsers\Java\Utils\MinMaxBounds;
use Celestriode\TargetSelectorConstructure\Context\Audits\Boolean;
use Celestriode\TargetSelectorConstructure\Context\Audits\HasKeyFromResourceRegistry;
use Celestriode\TargetSelectorConstructure\Context\Audits\HasValueFromRegistry;
use Celestriode\TargetSelectorConstructure\Context\Audits\HasValueFromResourceRegistry;
use Celestriode\TargetSelectorConstructure\Context\Audits\Negatable;
use Celestriode\TargetSelectorConstructure\Context\Audits\Numeric;
use Celestriode\TargetSelectorConstructure\Context\Audits\NumericRange;
use Celestriode\TargetSelectorConstructure\Context\Audits\RestrictedNegation;
use Celestriode\TargetSelectorConstructure\Context\Audits\StringLength;
use Celestriode\TargetSelectorConstructure\Context\Audits\StructureIsValue;
use Celestriode\TargetSelectorConstructure\Context\Audits\TypesMatch;
use Celestriode\TargetSelectorConstructure\Parsers\TargetSelectorParser;
use Celestriode\TargetSelectorConstructure\Structures\Selector;
use Celestriode\TargetSelectorConstructure\TargetSelectorConstructure;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class TargetSelectors implements ConstructuresInterface
{
    /**
     * The raw UUID of the entire target selector structure, necessary for nested recursion.
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
        $numericRange = new NumericRange(new MinMaxBounds(null, null));
        $numericRangePositive = new NumericRange(new MinMaxBounds(0, null));

        $dynamic = Selector::dynamic(SelectorTargets::get());

        $dynamic->getParameters()
            ->addValue('x', Selector::string()->addAudit(Numeric::get()))
            ->addValue('y', Selector::string()->addAudit(Numeric::get()))
            ->addValue('z', Selector::string()->addAudit(Numeric::get()))
            ->addValue('dx', Selector::string()->addAudit(Numeric::get()))
            ->addValue('dy', Selector::string()->addAudit(Numeric::get()))
            ->addValue('dz', Selector::string()->addAudit(Numeric::get()))
            ->addValue('tag', Selector::string()->negatable()->supportMultiple())
            ->addValue('team', Selector::string()->negatable()->supportMultiple())
            ->addValue('scores', Selector::nested()
                ->addValue(null, Selector::string()->addAudit($numericRange))
            )->addValue('distance', Selector::string()->addAudit($numericRangePositive))
            ->addValue('level', Selector::string()->addAudit($numericRangePositive))
            ->addValue('limit', Selector::string()->addAudit($numericRangePositive))
            ->addValue('sort', Selector::string()->addAudit(new HasValueFromRegistry(SelectorSorts::get())))
            ->addValue('gamemode', Selector::string()->negatable()->supportMultiple()->addAudit(new HasValueFromRegistry(Gamemodes::get())))
            ->addValue('name', Selector::string()->negatable()->supportMultiple())
            ->addValue('x_rotation', Selector::string()->addAudit($numericRange))
            ->addValue('y_rotation', Selector::string()->addAudit($numericRange))
            ->addValue('type', Selector::string()->negatable()->supportMultiple()->addAudits(new HasValueFromResourceRegistry(EntityTypes::get(), false, true, Tags::get(),true)))
            ->addValue('nbt', Selector::snbt()->negatable()->supportMultiple())
            ->addValue('predicate', Selector::string()->negatable()->supportMultiple())
            ->addValue('advancements', Selector::nested()
                ->addValue(null, Selector::mixed(
                    Selector::string()->addAudit(Boolean::get()),
                    Selector::nested()->addValue(null, Selector::string()->addAudits(Boolean::get())) // TODO: audit for key name being trigger identifiers.
                )->addAudit(new HasKeyFromResourceRegistry(Advancements::get(), true)))
            )->addAuditsToParameter('type', RestrictedNegation::get())
            ->addAuditsToParameter('name', RestrictedNegation::get())
            ->addAuditsToParameter('gamemode', RestrictedNegation::get())
            ->addAuditsToParameter('team', RestrictedNegation::get())
        ;

        return Selector::root(
            Selector::name()->addAudit(new StringLength(new MinMaxBounds(1, 16))), // TODO: allowed characters.
            Selector::uuid()->addAudit(AlwaysTrue::get()),
            $dynamic
        );
    }

    /**
     * Creates and returns a new parser specific to Java Edition. This includes overrides to ensure that specific parts
     * of the incoming raw selector are parsed correctly.
     *
     * @return TargetSelectorParser
     */
    public static function getParser(): TargetSelectorParser
    {
        $parser = new TargetSelectorParser();

        $parser->addOverride('nbt', TargetSelectorParser::forceSnbt());
        $parser->addOverride('type', TargetSelectorParser::forceValueUntil($parser->separator, $parser->delimiterClose, $parser->nestedDelimiterClose));
        $parser->addOverride('predicate', TargetSelectorParser::forceValueUntil($parser->separator, $parser->delimiterClose, $parser->nestedDelimiterClose));

        return $parser;
    }

    /**
     * Creates and returns a new constructure object for target selectors for Java Edition.
     *
     * @param EventHandlerInterface|null $eventHandler
     * @param TargetSelectorParser|null $parser
     * @return TargetSelectorConstructure
     */
    public static function getConstructure(EventHandlerInterface $eventHandler = null, TargetSelectorParser $parser = null): TargetSelectorConstructure
    {
        return new TargetSelectorConstructure($parser ?? static::getParser(), $eventHandler ?? new EventHandler(), TypesMatch::get()->addPredicate(StructureIsValue::get()), Negatable::get()->addPredicate(StructureIsValue::get()));
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
}