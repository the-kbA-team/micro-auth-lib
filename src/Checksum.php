<?php

namespace kbATeam\MicroAuthLib;

use DateTime;

/**
 * Class Checksum
 * Static methods to create checksums for requests to and responses.
 */
final class Checksum
{
    /**
     * Constants defining the checksum order.
     */
    public const REQUEST = '%ID%%SECRET%%REFERER%';
    public const RESPONSE = '%ID%%AUTH_NAME%%SECRET%%TIMESTAMP%';

    /**
     * @var string
     */
    private static $secret = '';

    /**
     * Set the shared secret.
     * @param string $secret
     */
    public static function setSecret(string $secret): void
    {
        self::$secret = $secret;
    }

    /**
     * Create the checksum for a request to kba-auth.
     * @param int $id
     * @param string $referer
     * @return string
     */
    public static function request(int $id, string $referer): string
    {
        $string = self::REQUEST;
        $string = str_replace('%ID%', $id, $string);
        $string = str_replace('%SECRET%', self::$secret, $string);
        $string = str_replace('%REFERER%', $referer, $string);
        return md5($string);
    }

    /**
     * Create the checksum for a response from kba-auth.
     * @param int $id
     * @param string $authName
     * @param DateTime $timestamp
     * @return string
     */
    public static function response(int $id, string $authName, DateTime $timestamp): string
    {
        $string = self::RESPONSE;
        $string = str_replace('%ID%', $id, $string);
        $string = str_replace('%AUTH_NAME%', $authName, $string);
        $string = str_replace('%SECRET%', self::$secret, $string);
        $string = str_replace('%TIMESTAMP%', $timestamp->format('U'), $string);
        return md5($string);
    }
}
