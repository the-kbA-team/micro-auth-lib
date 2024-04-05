<?php

namespace Tests\kbATeam\MicroAuthLib;

use kbATeam\MicroAuthLib\Exceptions\InvalidUrlException;
use kbATeam\MicroAuthLib\Url;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
    /**
     * Test whether toString works.
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testToString(): void
    {
        $url = new Url('https://www.google.com');
        static::assertSame('https://www.google.com/', (string)$url);
    }

    /**
     * Data provider of valid URLs.
     * @return array<array<string>>
     */
    public static function provideValidUrls(): array
    {
        return [
            ['https://www.google.com/'],
            ['https://foo:bar@www.google.com/'],
            ['https://foo@www.google.com/'],
            ['https://www.google.com:443/'],
            ['https://www.google.com/#imprint'],
            ['https://www.google.com/?foo=bar&bar=baz'],
        ];
    }

    /**
     * @param string $url
     * @throws InvalidUrlException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @dataProvider provideValidUrls
     */
    public function testValidUrls(string $url): void
    {
        static::assertSame(
            $url,
            (string)(new Url($url))
        );
    }

    /**
     * Add another parameter to a already packed URL.
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     */
    public function testSetParam(): void
    {
        $url = new Url('//me@www.duckduckgo.com:443/?s=what+is+my+ip#anchor');
        $url->setParam('x', 'hello world');
        static::assertSame(
            'https://me@www.duckduckgo.com:443/?s=what+is+my+ip&x=hello+world#anchor',
            (string)$url
        );
    }

    /**
     * Data provider of invalid URLs.
     * @return array<array<string>>
     */
    public static function provideInvalidUrls(): array
    {
        return [
            ['https://:80'],
            ['https://user@:80'],
            ['https:///www.google.com'],
        ];
    }

    /**
     * Test really messed up URLs.
     * @param string $string
     * @throws InvalidUrlException
     * @dataProvider provideInvalidUrls
     */
    public function testInvalidUrls(string $string): void
    {
        $this->expectException(InvalidUrlException::class);
        $this->expectExceptionMessage('Invalid URL.');
        new Url($string);
    }

    /**
     * Test missing hostname exception.
     */
    public function testMissingHostname(): void
    {
        $this->expectException(InvalidUrlException::class);
        $this->expectExceptionMessage('Missing hostname.');
        new Url('/path?q=search');
    }
}
