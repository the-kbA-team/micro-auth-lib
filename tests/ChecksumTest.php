<?php

namespace Tests\kbATeam\MicroAuthLib;

use DateTime;
use kbATeam\MicroAuthLib\Checksum;
use PHPUnit\Framework\TestCase;

class ChecksumTest extends TestCase
{
    /**
     * Test a simple request checksum without a shared secret.
     */
    public function testRequestWithoutSecret()
    {
        $actual = Checksum::request(39819, 'https://www.google.com');
        $expected = md5('39819https://www.google.com');
        static::assertSame($expected, $actual);
    }

    /**
     * Test a simple response checksum without a shared secret.
     */
    public function testResponseWithoutSecret()
    {
        $now = new DateTime('now');
        $actual = Checksum::response(56584, 'Alonese', $now);
        $expected = md5('56584Alonese' . $now->format('c'));
        static::assertSame($expected, $actual);
    }

    /**
     * Test a simple request checksum without a shared secret.
     */
    public function testRequestWithSecret()
    {
        Checksum::setSecret('lUfQ1lP7');
        $actual = Checksum::request(87660, 'https://www.github.com');
        $expected = md5('87660lUfQ1lP7https://www.github.com');
        static::assertSame($expected, $actual);
    }

    /**
     * Test a simple response checksum without a shared secret.
     */
    public function testResponseWithSecret()
    {
        $now = new DateTime('now');
        Checksum::setSecret('iDVig25J');
        $actual = Checksum::response(57136, 'Winfort', $now);
        $expected = md5('57136WinfortiDVig25J' . $now->format('c'));
        static::assertSame($expected, $actual);
    }
}
