<?php namespace Celestriode\ConstructuresMinecraft\Audits\Json;

use Celestriode\Constructure\AbstractConstructure;
use Celestriode\JsonConstructure\Context\Audits\AbstractStringAudit;
use Celestriode\JsonConstructure\Structures\Types\JsonString;

/**
 * Determines if an input is a valid URL, in the context of text components. Protocol is limited to HTTP and HTTPS and
 * is required.
 *
 * @package Celestriode\ConstructuresMinecraft\Audits\Json
 */
class ValidUrl extends AbstractStringAudit
{
    public const INVALID_PROTOCOL = 'cff23dd8-ccbb-435d-8cc0-612930a4f37f';
    public const INVALID_SYNTAX = '81781eb1-a58f-487e-b120-087041780463';

    public const PATTERN = '/^(http:\/\/|https:\/\/)([a-z0-9.]-?)*[a-z]+\.[a-z]+$/i';

    /**
     * @inheritDoc
     */
    protected function auditString(AbstractConstructure $constructure, JsonString $input, JsonString $expected): bool
    {
        $url = $input->getString();

        // Ensure the protocol exists and is either http:// or https://

        if (substr($url, 0, 7) != 'http://' && substr($url, 0, 8) != 'https://') {

            $constructure->getEventHandler()->trigger(self::INVALID_PROTOCOL, $this, $input, $expected);

            return false;
        }

        // Ensure that the rest of the URL is valid.

        if (!preg_match(self::PATTERN, $url)) {

            $constructure->getEventHandler()->trigger(self::INVALID_SYNTAX, $this, $input, $expected);

            return false;
        }

        // No issues, audit passes.

        return true;
    }

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return 'valid_url';
    }
}