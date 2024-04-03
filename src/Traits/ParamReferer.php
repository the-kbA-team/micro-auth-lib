<?php

namespace kbATeam\MicroAuthLib\Traits;

use kbATeam\MicroAuthLib\Exceptions\InvalidParameterException;
use kbATeam\MicroAuthLib\Exceptions\InvalidUrlException;
use kbATeam\MicroAuthLib\Url;

/**
 * Trait ParamReferer
 */
trait ParamReferer
{
    /**
     * @var Url
     */
    private $referer;

    /**
     * Get the referer URL for this request.
     * @return Url
     */
    public function getReferer(): Url
    {
        return $this->referer;
    }

    /**
     * Set the referer URL for this request.
     * @param Url $referer
     */
    public function setReferer(Url $referer): void
    {
        $this->referer = $referer;
    }

    /**
     * Read the referer parameter from the given input array.
     * @param array<string, string> $input
     * @return Url
     * @throws InvalidParameterException
     */
    private static function readReferer(array $input): Url
    {
        if (!array_key_exists(self::REFERER, $input)) {
            throw new InvalidParameterException('Request referer is missing.');
        }
        $referer = trim((string)filter_var(
            $input[self::REFERER],
            FILTER_SANITIZE_URL
        ));
        try {
            return new Url($referer);
        } catch (InvalidUrlException $exception) {
            throw new InvalidParameterException(
                'Invalid request referer: ' . $exception->getMessage(),
                0,
                $exception
            );
        }
    }
}
