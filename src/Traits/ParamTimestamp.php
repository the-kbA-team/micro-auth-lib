<?php

namespace kbATeam\MicroAuthLib\Traits;

use DateTime;
use kbATeam\MicroAuthLib\Exceptions\InvalidParameterException;

/**
 * Trait ParamTimestamp
 * Response timestamp parameter.
 */
trait ParamTimestamp
{
    /**
     * @var DateTime
     */
    private $timestamp;

    /**
     * @return DateTime
     */
    public function getTimestamp(): DateTime
    {
        return $this->timestamp;
    }

    /**
     * @param DateTime|null $timestamp
     */
    public function setTimestamp(DateTime $timestamp = null): void
    {
        if ($timestamp === null) {
            $timestamp = new DateTime('now');
        }
        $this->timestamp = $timestamp;
    }

    /**
     * Read the timestamp parameter from the given input array.
     * @param  array  $input
     * @return DateTime
     * @throws InvalidParameterException
     */
    private static function readTimestamp(array $input): DateTime
    {
        if (!array_key_exists(self::TIMESTAMP, $input)) {
            throw new InvalidParameterException('Parameter timestamp is missing.');
        }
        $timestamp = DateTime::createFromFormat('U', $input[self::TIMESTAMP]);
        if (!$timestamp instanceof DateTime) {
            throw new InvalidParameterException(
                'Parameter timestamp is not a timestamp.'
            );
        }
        return $timestamp;
    }
}
