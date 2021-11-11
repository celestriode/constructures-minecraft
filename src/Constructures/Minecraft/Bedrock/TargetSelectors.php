<?php namespace Celestriode\ConstructuresMinecraft\Constructures\Minecraft\Bedrock;

use Celestriode\Constructure\Structures\StructureInterface;
use Celestriode\ConstructuresMinecraft\Constructures\Minecraft\Java\TargetSelectors as JavaTargetSelectors;
use Celestriode\DynamicMinecraftRegistries\Bedrock\Game\Gamemodes;
use Celestriode\DynamicMinecraftRegistries\Bedrock\Game\Registries\EntityFamilies;
use Celestriode\DynamicMinecraftRegistries\Bedrock\Game\Registries\EntityTypes;
use Celestriode\DynamicMinecraftRegistries\Bedrock\Other\SelectorTargets;
use Celestriode\DynamicRegistry\Exception\InvalidValue;
use Celestriode\Mattock\Parsers\Java\Utils\MinMaxBounds;
use Celestriode\TargetSelectorConstructure\Context\Audits\HasValueFromRegistry;
use Celestriode\TargetSelectorConstructure\Context\Audits\HasValueFromResourceRegistry;
use Celestriode\TargetSelectorConstructure\Context\Audits\Numeric;
use Celestriode\TargetSelectorConstructure\Context\Audits\NumericRange;
use Celestriode\TargetSelectorConstructure\Context\Audits\RestrictedNegation;
use Celestriode\TargetSelectorConstructure\Context\Audits\StringLength;
use Celestriode\TargetSelectorConstructure\Parsers\TargetSelectorParser;
use Celestriode\TargetSelectorConstructure\Structures\Selector;
use Ramsey\Uuid\UuidInterface;

class TargetSelectors extends JavaTargetSelectors
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
        $numericPositive = new Numeric(new MinMaxBounds(0, null));

        $dynamic = Selector::dynamic(SelectorTargets::get());

        // scores, family

        $dynamic->getParameters()
            ->addValue('x', Selector::string()->addAudit(Numeric::get()))
            ->addValue('y', Selector::string()->addAudit(Numeric::get()))
            ->addValue('z', Selector::string()->addAudit(Numeric::get()))
            ->addValue('dx', Selector::string()->addAudit(Numeric::get()))
            ->addValue('dy', Selector::string()->addAudit(Numeric::get()))
            ->addValue('dz', Selector::string()->addAudit(Numeric::get()))
            ->addValue('rx', Selector::string()->addAudit(Numeric::get()))
            ->addValue('rxm', Selector::string()->addAudit(Numeric::get()))
            ->addValue('ry', Selector::string()->addAudit(Numeric::get()))
            ->addValue('rym', Selector::string()->addAudit(Numeric::get()))
            ->addValue('tag', Selector::string()->negatable()->supportMultiple())
            ->addValue('scores', Selector::nested()
                ->addValue(null, Selector::string()->negatable()->addAudit($numericRange))
            )
            ->addValue('l', Selector::string()->addAudit($numericPositive))
            ->addValue('lm', Selector::string()->addAudit($numericPositive))
            ->addValue('r', Selector::string()->addAudit($numericPositive))
            ->addValue('rm', Selector::string()->addAudit($numericPositive))
            ->addValue('c', Selector::string()->addAudit($numericPositive))
            ->addValue('m', Selector::string()->negatable()->supportMultiple()->addAudit(new HasValueFromRegistry(Gamemodes::get())))
            ->addValue('name', Selector::string()->negatable()->supportMultiple())
            ->addValue('family', Selector::string()->negatable()->supportMultiple()->addAudits(new HasValueFromResourceRegistry(EntityFamilies::get(), true)))
            ->addValue('type', Selector::string()->negatable()->supportMultiple()->addAudits(new HasValueFromResourceRegistry(EntityTypes::get(), true, false)))
            ->addAuditsToParameter('type', RestrictedNegation::get())
            ->addAuditsToParameter('name', RestrictedNegation::get())
        ;

        return Selector::root(
            Selector::name()->addAudit(new StringLength(new MinMaxBounds(1, 16))), // TODO: allowed characters.
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

        $parser->addOverride('type', TargetSelectorParser::forceValueUntil($parser->separator, $parser->delimiterClose, $parser->nestedDelimiterClose));

        return $parser;
    }
}