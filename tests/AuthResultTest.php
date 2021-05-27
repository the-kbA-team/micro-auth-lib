<?php

namespace Tests\kbATeam\MicroAuthLib;

use kbATeam\MicroAuthLib\AuthResult;
use kbATeam\MicroAuthLib\Exceptions\InvalidParameterException;
use PHPUnit\Framework\TestCase;

class AuthResultTest extends TestCase
{

    public function testRead()
    {
        $server['REMOTE_USER'] = 'Protiong';
        $result = AuthResult::read($server);
        static::assertInstanceOf(AuthResult::class, $result);
        static::assertSame('Protiong', $result->getAuthName());
    }

    public static function provideInvalidAuthNames(): array
    {
        return [
            [[], 'Authenticated name is missing.'],
            [['REMOTE_USER' => ''], 'Authenticated name is empty.']
        ];
    }

    /**
     * @param array $input
     * @param string $message
     * @throws InvalidParameterException
     * @dataProvider provideInvalidAuthNames
     */
    public function testInvalidAuthNames(array $input, string $message)
    {
        $this->expectException(InvalidParameterException::class);
        $this->expectExceptionMessage($message);
        AuthResult::read($input);
    }

    public function testSetterAndGetter()
    {
        $result = new AuthResult('Andly2000');
        $result->setAuthName('Appose');
        static::assertSame('Appose', $result->getAuthName());
    }
}
