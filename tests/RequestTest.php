<?php

namespace Tests\kbATeam\MicroAuthLib;

use kbATeam\MicroAuthLib\Exceptions\InvalidParameterException;
use kbATeam\MicroAuthLib\Request;
use kbATeam\MicroAuthLib\Url;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

/**
 * Class RequestTest
 * The main tests are done by the RequestResponseTest.
 * These are additional tests for code specific to the Request class.
 */
class RequestTest extends TestCase
{
    /**
     * Manipulate ID parameter in order to cause the checksum test to fail.
     * @throws InvalidParameterException
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testRequestChecksumFail()
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
        $requestParams[Request::ID] = 1;

        $this->expectException(InvalidParameterException::class);
        $this->expectExceptionMessage('Parameter check failed.');
        Request::read($requestParams);
    }
}
