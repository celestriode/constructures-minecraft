<?php namespace Celestriode\ConstructuresMinecraft\Constructures\Minecraft\Bedrock;

use Celestriode\Constructure\Context\AuditInterface;
use Celestriode\Constructure\Structures\StructureInterface;
use Celestriode\ConstructuresMinecraft\Audits\Json\HasValueFromRegistry;
use Celestriode\ConstructuresMinecraft\Audits\Json\ValidSelector;
use Celestriode\ConstructuresMinecraft\Constructures\Minecraft\Java\TextComponents as JavaTextComponents;
use Celestriode\ConstructuresMinecraft\Utils\EnumEdition;
use Celestriode\DynamicMinecraftRegistries\Bedrock\Resources\Translations;
use Celestriode\DynamicRegistry\Exception\InvalidValue;
use Celestriode\JsonConstructure\Context\Audits\Branch;
use Celestriode\JsonConstructure\Context\Audits\ExclusiveFields;
use Celestriode\JsonConstructure\Context\Audits\InclusiveFields;
use Celestriode\JsonConstructure\Context\Audits\NumberRange;
use Celestriode\JsonConstructure\Utils\Json;

/**
 *
 * Expected structure for text components. Use getStructure() for the whole thing.
 *
 * @package Celestriode\ConstructuresMinecraft\Constructures\Minecraft\Bedrock
 */
class TextComponents extends JavaTextComponents
{
    protected const UUID = 'ccaf325c-ad0e-4054-a6c3-b6fe13cf1e2f';
    protected static $uuid;

    /**
     * @inheritDoc
     */
    public static function getStructure(): StructureInterface
    {
        $structure = Json::object()->setUUID(self::getUUID());
        $selector = new ValidSelector(EnumEdition::BEDROCK);

        $structure->failOnUnexpectedKeys(true)
            ->addChild('rawtext', Json::array()->addElement(Json::object()
                ->addAudits(static::getTextAudit(), static::getTranslationBranch())
                ->addChild('text', Json::string())
                ->addChild('selector', Json::string()->addAudit($selector))
                ->addChild('translate', Json::string()->addAudit(static::getTranslationAudit()))
                ->addChild('score', Json::object()
                    ->addChild('name', Json::string()->required()->addAudit($selector))
                    ->addChild('objective', Json::string()->required()->addAudit(new NumberRange(1, 16))) // TODO: verify
                )
            ));

        return $structure;
    }

    /**
     * Returns audit that ensures some text to display is provided, and only one of such is given.
     *
     * @return AuditInterface
     */
    protected static function getTextAudit(): AuditInterface
    {
        return (new ExclusiveFields('text', 'selector', 'translate', 'score'))->required();
    }

    /**
     * Returns the branch for translations, specifically to inject "with".
     *
     * @return AuditInterface
     */
    protected static function getTranslationBranch(): AuditInterface
    {
        $branch = Json::object()
            ->addChild('with', Json::mixed(null,
                Json::array()->addElement(Json::string()),
                Json::redirect(self::getUUID())
            )
        );

        return new Branch('translate', $branch, new InclusiveFields('translate'));
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
}