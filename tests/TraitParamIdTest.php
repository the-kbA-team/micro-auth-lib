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
 * Class TraitParamIdTest
 * The main tests are done by the RequestResponseTest.
 * These are additional tests for code specific to the ParamId trait.
 */
class TraitParamIdTest extends TestCase
{
    /**
     * Remove ID parameter.
     * @throws InvalidParameterException
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testMissingIdParameter()
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
        static::assertIsString($requestParsed['query']);

        parse_str($requestParsed['query'], $requestParams);

        static::assertIsArray($requestParams);

        /**
         * Manipulate params
         */
        unset($requestParams[Request::ID]);

        $this->expectException(InvalidParameterException::class);
        $this->expectExceptionMessage('ID is missing.');
        Request::read($requestParams);
    }

    /**
     * Data provider for invalid ID parameters.
     * @return array
     */
    public static function provideInvalidIdParameter(): array
    {
        return [
            ['zYcvkptn'],
            [null],
            [[35342]],
            [new stdClass()],
        ];
    }

    /**
     * Replace ID parameter with anything but a number.
     * @param mixed $replacement
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @throws InvalidParameterException
     * @dataProvider provideInvalidIdParameter
     */
    public function testInvalidIdParameter($replacement)
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
        static::assertIsString($requestParsed['query']);

        parse_str($requestParsed['query'], $requestParams);

        static::assertIsArray($requestParams);

        /**
         * Replace ID parameter with something invalid.
         */
        $requestParams[Request::ID] = $replacement;

        $this->expectException(InvalidParameterException::class);
        $this->expectExceptionMessage('ID is not an integer.');
        Request::read($requestParams);
    }
}
