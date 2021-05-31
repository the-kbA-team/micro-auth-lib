<?php

namespace Tests\kbATeam\MicroAuthLib;

use DateTime;
use kbATeam\MicroAuthLib\AuthResult;
use kbATeam\MicroAuthLib\Exceptions\InvalidParameterException;
use kbATeam\MicroAuthLib\Request;
use kbATeam\MicroAuthLib\Response;
use kbATeam\MicroAuthLib\Url;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use SebastianBergmann\RecursionContext\InvalidArgumentException;

/**
 * Class RequestResponseTest
 * Proof that Request and Response classes interact correctly.
 */
class RequestResponseTest extends TestCase
{
    public const APP_URL = 'https://app.test/user/login';
    public const AUTH_URL = 'https://auth.service.test/auth.php';

    /**
     * @throws InvalidParameterException
     * @throws Exception
     * @throws ExpectationFailedException
     * @throws InvalidArgumentException
     */
    public function testRequestResponse()
    {
        $requestId = rand(1000, 9999);

        /**
         * Build request on the client side.
         */
        $requestSend = new Request(new Url(self::APP_URL), $requestId);
        $requestLocation = $requestSend->getLocation(new Url(self::AUTH_URL));

        static::assertIsString($requestLocation);

        /**
         * Parse request location and assert its parts.
         */
        $requestParsed = parse_url($requestLocation);
        static::assertIsArray($requestParsed);
        static::assertArrayHasKey('scheme', $requestParsed);
        static::assertArrayHasKey('host', $requestParsed);
        static::assertArrayHasKey('path', $requestParsed);
        static::assertArrayHasKey('query', $requestParsed);
        static::assertSame('https', $requestParsed['scheme']);
        static::assertSame('auth.service.test', $requestParsed['host']);
        static::assertSame('/auth.php', $requestParsed['path']);
        static::assertIsString($requestParsed['query']);

        /**
         * Parse GET string into an array
         */
        parse_str($requestParsed['query'], $requestParams);
        static::assertIsArray($requestParams);

        /**
         * Accept request on the server side.
         */
        $requestReceived = Request::read($requestParams);

        /**
         * Assert that the referer an the ID have been parsed correctly.
         */
        static::assertSame(self::APP_URL, (string)$requestReceived->getReferer());
        static::assertSame($requestId, $requestReceived->getId());

        /**
         * Build Apache2 authentication result.
         */
        $server[AuthResult::AUTH_NAME] = 'Mailly';
        $authResult = AuthResult::read($server);

        $responseTime = new DateTime('now');

        /**
         * Build response on the server side
         */
        $responseSend = new Response($authResult->getAuthName(), $requestReceived->getId(), $responseTime);
        $responseLocation = $responseSend->getLocation($requestReceived->getReferer());

        static::assertIsString($responseLocation);

        /**
         * Parse response location and its parts.
         */
        $responseParsed = parse_url($responseLocation);
        static::assertIsArray($responseParsed);
        static::assertArrayHasKey('scheme', $responseParsed);
        static::assertArrayHasKey('host', $responseParsed);
        static::assertArrayHasKey('path', $responseParsed);
        static::assertArrayHasKey('query', $responseParsed);
        static::assertSame('https', $responseParsed['scheme']);
        static::assertSame('app.test', $responseParsed['host']);
        static::assertSame('/user/login', $responseParsed['path']);
        static::assertIsString($responseParsed['query']);

        /**
         * Parse GET string into an array
         */
        parse_str($responseParsed['query'], $responseParams);
        static::assertIsArray($responseParams);

        /**
         * Accept the response on the client side
         */
        $responseReceived = Response::read($responseParams);

        /**
         * Assert that the authenticated name, ID and timestamp have been parsed correctly.
         */
        static::assertSame('Mailly', $responseReceived->getAuthName());
        static::assertSame($requestId, $responseReceived->getId());
        static::assertSame($responseTime->format('U'), $responseReceived->getTimestamp()->format('U'));
    }
}
