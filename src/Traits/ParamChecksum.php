<?php

namespace kbATeam\MicroAuthLib\Traits;

use kbATeam\MicroAuthLib\Exceptions\InvalidParameterException;

/**
 * Trait ParamChecksum
 * Request and Response checksum parameter.
 */
trait ParamChecksum
{
    /**
     * Read the checksum parameter from the given input array.
     * @param array $input
     * @return string
     * @throws InvalidParameterException
     */
    private static function readChecksum(array $input): string
    {
        if (!array_key_exists(self::CHECKSUM, $input)) {
            throw new InvalidParameterException('Checksum is missing.');
        }
        $checksum = trim((string)filter_var(
            $input[self::CHECKSUM],
            FILTER_SANITIZE_STRING,
            FILTER_FLAG_STRIP_LOW|FILTER_FLAG_STRIP_HIGH|FILTER_FLAG_STRIP_BACKTICK
        ));
        if (empty($checksum)) {
            throw new InvalidParameterException('Checksum is empty.');
        }
        return $checksum;
    }
}
