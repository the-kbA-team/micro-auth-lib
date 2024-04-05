<?php

namespace Tests\kbATeam\MicroAuthLib;

use kbATeam\MicroAuthLib\Exceptions\InvalidParameterException;
use kbATeam\MicroAuthLib\Request;
use kbATeam\MicroAuthLib\Url;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;
use stdClass;

/**
 * Class TraitParamRefererTest
 * The main tests are done by the RequestResponseTest.
 * These are additional tests for code specific to the ParamReferer trait.
 */
class TraitParamRefererTest extends TestCase
{
    /**
     * @throws InvalidParameterException
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testMissingRefererParameter(): void
    {
        /**
         * Build request on the client side with a random ID
         */
        $requestSend = new Request(new Url('https://app.test/user/login'));
        $requestLocation = $requestSend->getLocation(new Url('https://auth.service.test/auth.php'));

        static::assertIsString($requestLocation);

        /**
         * Parse GET parameters from location
         */
        $requestParsed = parse_url($requestLocation);

        static::assertIsArray($requestParsed);
        static::assertArrayHasKey('query', $requestParsed);
        $queryValue = $requestParsed['query'] ?? null;
        static::assertIsString($queryValue);

        parse_str($queryValue, $requestParams);

        static::assertIsArray($requestParams);

        /**
         * Manipulate params
         */
        unset($requestParams[Request::REFERER]);

        $this->expectException(InvalidParameterException::class);
        $this->expectExceptionMessage('Request referer is missing.');
        Request::read($requestParams);
    }

    /**
     * Data provider of invalid referer parameters.
     * @return array<int, mixed>
     */
    public static function provideInvalidRefererParameter(): array
    {
        return [
            ['l4LgeEm1gD'],
            [57754],
            [8401.1],
            [true],
            [false],
            [null],
            [['https://www.google.com/']],
            [new stdClass()],
            ['https://:80'],
            ['https://user@:80'],
            ['https:///www.google.com'],
        ];
    }

    /**
     * Replace referer parameter with anything but a URL.
     * @param mixed $replacement
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws InvalidParameterException
     * @dataProvider provideInvalidRefererParameter
     */
    public function testInvalidRefererParameter($replacement): void
    {
        /**
         * Build request on the client side with a random ID
         */
        $requestSend = new Request(new Url('https://app.test/user/login'));
        $requestLocation = $requestSend->getLocation(new Url('https://auth.service.test/auth.php'));

        static::assertIsString($requestLocation);

        /**
         * Parse GET parameters from location
         */
        $requestParsed = parse_url($requestLocation);

        static::assertIsArray($requestParsed);
        static::assertArrayHasKey('query', $requestParsed);
        $queryValue = $requestParsed['query'] ?? null;
        static::assertIsString($queryValue);

        parse_str($queryValue, $requestParams);

        static::assertIsArray($requestParams);

        /**
         * Replace ID parameter with something invalid.
         */
        $requestParams[Request::REFERER] = $replacement;

        $this->expectException(InvalidParameterException::class);
        $this->expectExceptionMessage('Invalid request referer:');
        Request::read($requestParams);
    }
}
