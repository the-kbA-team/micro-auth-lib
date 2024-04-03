<?php

namespace kbATeam\MicroAuthLib;

use kbATeam\MicroAuthLib\Exceptions\InvalidUrlException;

/**
 * Class Url
 * Handling GET parameters of service and referer URLs.
 */
final class Url
{
    /**
     * @var array
     */
    private $url;

    /**
     * @var array
     */
    private $params = [];

    /**
     * Referer constructor.
     * @param string $url
     * @throws InvalidUrlException
     */
    public function __construct(string $url)
    {
        $this->url = parse_url($url);
        if ($this->url === false) {
            throw new InvalidUrlException('Invalid URL.');
        }
        if (!array_key_exists('host', $this->url) || $this->url['host'] === null) {
            throw new InvalidUrlException('Missing hostname.');
        }
        if (array_key_exists('query', $this->url) && $this->url['query'] !== null) {
            parse_str($this->url['query'], $this->params);
        }
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function setParam(string $key, string $value): void
    {
        $this->params[$key] = filter_var(
            $value,
            FILTER_SANITIZE_STRING,
            FILTER_FLAG_STRIP_HIGH|FILTER_FLAG_STRIP_LOW|FILTER_FLAG_STRIP_BACKTICK
        );
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getScheme()
            . $this->getUserPass()
            . $this->url['host']
            . $this->getPort()
            . $this->getPath()
            . $this->getQuery()
            . $this->getFragment();
    }

    /**
     * @return string
     */
    private function getScheme(): string
    {
        if (!array_key_exists('scheme', $this->url) || $this->url['scheme'] === null) {
            return 'https://';
        }
        return $this->url['scheme'] . '://';
    }

    /**
     * @return string
     */
    private function getUserPass(): string
    {
        if (!array_key_exists('user', $this->url) || $this->url['user'] === null) {
            return '';
        }
        if (!array_key_exists('pass', $this->url) || $this->url['pass'] === null) {
            return $this->url['user'] . '@';
        }
        return $this->url['user']
            . ':'
            . $this->url['pass']
            . '@';
    }

    /**
     * @return string
     */
    private function getPort(): string
    {
        if (!array_key_exists('port', $this->url) || $this->url['port'] === null) {
            return '';
        }
        return ':' . $this->url['port'];
    }

    /**
     * @return string
     */
    private function getPath(): string
    {
        if (!array_key_exists('path', $this->url) || $this->url['path'] === null) {
            return '/';
        }
        return $this->url['path'];
    }

    /**
     * @return string
     */
    private function getQuery(): string
    {
        if ($this->params === []) {
            return '';
        }
        return '?' . http_build_query($this->params);
    }

    /**
     * @return string
     */
    private function getFragment(): string
    {
        if (!array_key_exists('fragment', $this->url) || $this->url['fragment'] === null) {
            return '';
        }
        return '#' . $this->url['fragment'];
    }
}
