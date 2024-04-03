<?php

namespace kbATeam\MicroAuthLib\Traits;

use kbATeam\MicroAuthLib\Exceptions\InvalidParameterException;

/**
 * Trait ParamAuthName
 */
trait ParamAuthName
{
    /**
     * @var string
     */
    private $authName;

    /**
     * @return string
     */
    public function getAuthName(): string
    {
        return $this->authName;
    }

    /**
     * @param string $authName
     */
    public function setAuthName(string $authName): void
    {
        $this->authName = $authName;
    }

    /**
     * Read the authenticated name parameter from the given input array.
     * @param array<string, string> $input
     * @return string
     * @throws InvalidParameterException
     */
    private static function readAuthName(array $input): string
    {
        if (!array_key_exists(self::AUTH_NAME, $input)) {
            throw new InvalidParameterException('Authenticated name is missing.');
        }
        $authName = trim((string)filter_var(
            $input[self::AUTH_NAME],
            FILTER_SANITIZE_STRING,
            FILTER_FLAG_STRIP_HIGH|FILTER_FLAG_STRIP_LOW|FILTER_FLAG_STRIP_BACKTICK
        ));
        if (empty($authName)) {
            throw new InvalidParameterException('Authenticated name is empty.');
        }
        return $authName;
    }
}
