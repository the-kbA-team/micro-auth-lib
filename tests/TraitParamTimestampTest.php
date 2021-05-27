<?php

namespace Tests\kbATeam\MicroAuthLib;

use kbATeam\MicroAuthLib\Exceptions\InvalidParameterException;
use kbATeam\MicroAuthLib\Response;
use kbATeam\MicroAuthLib\Url;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

/**
 * Class TraitParamTimestampTest
 * The main tests are done by the RequestResponseTest.
 * These are additional tests for code specific to the ParamReferer trait.
 */
class TraitParamTimestampTest extends TestCase
{
    /**
     * Remove timestamp parameter.
     * @throws InvalidParameterException
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testMissingTimestampParameter()
    {
        /**
         * Build response on the server side with a random ID.
         */
        $responseSend = new Response('Frimilt');
        $responseLocation = $responseSend->getLocation(new Url('https://app.test/user/login'));

        static::assertIsString($responseLocation);

        /**
         * Parse GET parameters from location
         */
        $responseParsed = parse_url($responseLocation);
        static::assertIsArray($responseParsed);
        static::assertArrayHasKey('query', $responseParsed);
        static::assertIsString($responseParsed['query']);

        parse_str($responseParsed['query'], $responseParams);

        static::assertIsArray($responseParams);

        /**
         * Manipulate params
         */
        unset($responseParams[Response::TIMESTAMP]);

        $this->expectException(InvalidParameterException::class);
        $this->expectExceptionMessage('Parameter timestamp is missing.');
        Response::read($responseParams);
    }

    /**
     * Replace timestamp parameter with a string.
     * @throws InvalidParameterException
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testInvalidTimestampParameter()
    {
        /**
         * Build response on the server side with a random ID.
         */
        $responseSend = new Response('Frimilt');
        $responseLocation = $responseSend->getLocation(new Url('https://app.test/user/login'));

        static::assertIsString($responseLocation);

        /**
         * Parse GET parameters from location
         */
        $responseParsed = parse_url($responseLocation);
        static::assertIsArray($responseParsed);
        static::assertArrayHasKey('query', $responseParsed);
        static::assertIsString($responseParsed['query']);

        parse_str($responseParsed['query'], $responseParams);

        static::assertIsArray($responseParams);

        /**
         * Manipulate params
         */
        $responseParams[Response::TIMESTAMP] = 'Wdz91jv98J';

        $this->expectException(InvalidParameterException::class);
        $this->expectExceptionMessage('Parameter timestamp is not a timestamp.');
        Response::read($responseParams);
    }
}
