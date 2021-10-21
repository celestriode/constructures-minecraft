<?php namespace Celestriode\ConstructuresMinecraft\Audits;

use Celestriode\ConstructuresMinecraft\Utils\EnumEdition;

/**
 * Holds an enumeration value for the edition in context. Used with traits that split based on edition.
 *
 * @package Celestriode\ConstructuresMinecraft\Audits
 */
trait MinecraftAuditTrait
{
    /**
     * @var int The edition being used.
     */
    protected $edition = EnumEdition::ANY;

    /**
     * Sets the edition to use.
     *
     * @param int $edition
     */
    public function setEdition(int $edition): void
    {
        $this->edition = $edition;
    }

    /**
     * Returns the edition being used.
     *
     * @return int
     */
    public function getEdition(): int
    {
        return $this->edition;
    }

    /**
     * Creates a string to use for the toString method for audits.
     *
     * @return string
     */
    public function getEditionString(): string
    {
        return 'edition=' . $this->getEdition();
    }
}