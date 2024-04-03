<?php

namespace kbATeam\MicroAuthLib;

use DateInterval;
use DateTime;
use Exception;
use kbATeam\MicroAuthLib\Exceptions\InvalidParameterException;
use kbATeam\MicroAuthLib\Traits\ParamAuthName;
use kbATeam\MicroAuthLib\Traits\ParamChecksum;
use kbATeam\MicroAuthLib\Traits\ParamId;
use kbATeam\MicroAuthLib\Traits\ParamTimestamp;

/**
 * Class Response
 * Kerberos SSO authentication response.
 */
final class Response
{
    /**
     * Constants defining the GET parameter names of the response.
     */
    public const AUTH_NAME = 'n';
    public const TIMESTAMP = 't';
    public const ID = 'i';
    public const CHECKSUM = 'c';

    use ParamId;
    use ParamChecksum;
    use ParamAuthName;
    use ParamTimestamp;

    /**
     * Response constructor.
     * @param string $authName
     * @param DateTime|null $timestamp
     * @param int|null $id
     */
    public function __construct(string $authName, int $id = null, DateTime $timestamp = null)
    {
        $this->setAuthName($authName);
        $this->setTimestamp($timestamp);
        $this->setId($id);
    }

    /**
     * Generate the checksum of this Response instance.
     * @return string
     */
    public function getChecksum(): string
    {
        return Checksum::response($this->getId(), $this->getAuthName(), $this->getTimestamp());
    }

    /**
     * Generate the location URL to the given referer for this response.
     * @param Url $referer
     * @return string
     */
    public function getLocation(Url $referer): string
    {
        $referer->setParam(self::AUTH_NAME, $this->getAuthName());
        $referer->setParam(self::TIMESTAMP, $this->getTimestamp()->format('U'));
        $referer->setParam(self::ID, $this->getId());
        $referer->setParam(self::CHECKSUM, $this->getChecksum());
        return (string)$referer;
    }

    /**
     * Read the response parameters from the input array and return a Response instance.
     * @param array $input
     * @param int $timeoutSeconds
     * @return Response
     * @throws InvalidParameterException
     * @throws Exception
     */
    public static function read(array $input, int $timeoutSeconds = 5): Response
    {
        $authName = self::readAuthName($input);
        $id = self::readId($input);
        $timestamp = self::readTimestamp($input);
        if (Checksum::response($id, $authName, $timestamp) !== self::readChecksum($input)) {
            throw new InvalidParameterException('Parameter check failed.');
        }

        $lowerLimit = (new DateTime('now'))
            ->sub(new DateInterval(sprintf('PT%uS', $timeoutSeconds)));
        $upperLimit = new DateTime('now');

        /**
         * The timezone of $timestamp will be UTC, because of format 'U'. The timezone of the local variables
         * $lowerLimit and $upperLimit will be the one set by the local instance of PHP. Apply the local timezone
         * to the timestamp from the response.
         */
        $timestamp = $timestamp->setTimezone($lowerLimit->getTimezone());

        if ($timestamp < $lowerLimit || $upperLimit < $timestamp) {
            throw new InvalidParameterException('Response has timed out.');
        }
        return new self($authName, $id, $timestamp);
    }
}
