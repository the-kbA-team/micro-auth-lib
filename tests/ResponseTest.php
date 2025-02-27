<?php

namespace Tests\kbATeam\MicroAuthLib;

use DateInterval;
use DateTime;
use kbATeam\MicroAuthLib\Exceptions\InvalidParameterException;
use kbATeam\MicroAuthLib\Response;
use kbATeam\MicroAuthLib\Url;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

/**
 * Class ResponseTest
 * The main tests are done by the RequestResponseTest.
 * These are additional tests for code specific to the Response class.
 */
class ResponseTest extends TestCase
{
    /**
     * Manipulate ID parameter in order to cause the checksum test to fail.
     * @throws InvalidParameterException
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testResponseChecksumFail(): void
    {
        /**
         * Build response on the server side with a random ID.
         */
        $responseSend = new Response('Overed');
        $responseLocation = $responseSend->getLocation(new Url('https://app.test/user/login'));

        /**
         * Parse GET parameters from location
         */
        $responseParsed = parse_url($responseLocation);
        static::assertIsArray($responseParsed);
        static::assertArrayHasKey('query', $responseParsed);
        static::assertIsString($responseParsed['query'] ?? null);

        parse_str($responseParsed['query'], $responseParams);

        /**
         * Manipulate params
         */
        $responseParams[Response::ID] = 1;

        $this->expectException(InvalidParameterException::class);
        $this->expectExceptionMessage('Parameter check failed.');
        Response::read($responseParams);
    }

    /**
     * Data provider for a response timeout.
     * @return array<int, array<int, DateTime>>
     */
    public static function provideResponseTimeout(): array
    {
        return [
            //6 seconds into the past
            [(new DateTime('now'))->sub(new DateInterval('PT6S'))],
            //2 seconds into the future
            [(new DateTime('now'))->add(new DateInterval('PT2S'))]
        ];
    }

    /**
     * Manipulate ID parameter in order to cause the checksum test to fail.
     * @throws InvalidParameterException
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     * @dataProvider provideResponseTimeout
     */
    public function testResponseTimeout(DateTime $timestamp): void
    {
        /**
         * Build response on the server side with a random ID.
         */
        $responseSend = new Response('Overed', null, $timestamp);
        $responseLocation = $responseSend->getLocation(new Url('https://app.test/user/login'));

        /**
         * Parse GET parameters from location
         */
        $responseParsed = parse_url($responseLocation);
        static::assertIsArray($responseParsed);
        static::assertArrayHasKey('query', $responseParsed);
        static::assertIsString($responseParsed['query'] ?? null);

        parse_str($responseParsed['query'], $responseParams);

        $this->expectException(InvalidParameterException::class);
        $this->expectExceptionMessage('Response has timed out.');
        Response::read($responseParams);
    }
}
