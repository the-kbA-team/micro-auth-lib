<?php

namespace kbATeam\MicroAuthLib;

use kbATeam\MicroAuthLib\Exceptions\InvalidParameterException;
use kbATeam\MicroAuthLib\Traits\ParamChecksum;
use kbATeam\MicroAuthLib\Traits\ParamId;
use kbATeam\MicroAuthLib\Traits\ParamReferer;

/**
 * Class Request
 * Kerberos SSO authentication request.
 */
class Request
{
    /**
     * Constants defining the GET parameter names of the request.
     */
    public const ID = 'i';
    public const REFERER = 'r';
    public const CHECKSUM = 'c';

    use ParamId;
    use ParamChecksum;
    use ParamReferer;

    /**
     * Construct a request with the given ID and URL.
     * @param Url $referer
     * @param int|null $id
     */
    public function __construct(Url $referer, int $id = null)
    {
        $this->setId($id);
        $this->setReferer($referer);
    }

    /**
     * Generate the checksum of this Request instance.
     * @return string
     */
    public function getChecksum(): string
    {
        return Checksum::request($this->getId(), (string)$this->getReferer());
    }

    /**
     * Generate the location URL to the given service for this request.
     * @param Url $service
     * @return string
     */
    public function getLocation(Url $service): string
    {
        $service->setParam(static::ID, $this->getId());
        $service->setParam(static::REFERER, (string)$this->getReferer());
        $service->setParam(static::CHECKSUM, $this->getChecksum());
        return (string)$service;
    }

    /**
     * Read the request parameters from the input array and return a Request instance.
     * @param array $input Input array, like $_GET.
     * @return Request
     * @throws InvalidParameterException
     */
    public static function read(array $input): Request
    {
        $id = static::readId($input);
        $referer = static::readReferer($input);
        if (Checksum::request($id, (string)$referer) !== static::readChecksum($input)) {
            throw new InvalidParameterException('Parameter check failed.');
        }
        return new static($referer, $id);
    }
}
