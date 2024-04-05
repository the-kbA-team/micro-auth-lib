<?php

namespace Tests\kbATeam\MicroAuthLib;

use kbATeam\MicroAuthLib\AuthResult;
use kbATeam\MicroAuthLib\Exceptions\InvalidParameterException;
use PHPUnit\Framework\TestCase;

/**
 * Class AuthResultTest
 */
class AuthResultTest extends TestCase
{
    /**
     * @return void
     */
    public function testRead(): void
    {
        $server['REMOTE_USER'] = 'Protiong';
        $result = AuthResult::read($server);
        static::assertInstanceOf(AuthResult::class, $result);
        static::assertSame('Protiong', $result->getAuthName());
    }

    /**
     * @return array<int, array<int, array<string, string>|string>>
     */
    public static function provideInvalidAuthNames(): array
    {
        return [
            [[], 'Authenticated name is missing.'],
            [['REMOTE_USER' => ''], 'Authenticated name is empty.']
        ];
    }

    /**
     * @param array<int, mixed> $input
     * @param string $message
     * @throws InvalidParameterException
     * @dataProvider provideInvalidAuthNames
     */
    public function testInvalidAuthNames(array $input, string $message): void
    {
        $this->expectException(InvalidParameterException::class);
        $this->expectExceptionMessage($message);
        AuthResult::read($input);
    }

    /**
     * @return void
     */
    public function testSetterAndGetter(): void
    {
        $result = new AuthResult('Andly2000');
        $result->setAuthName('Appose');
        static::assertSame('Appose', $result->getAuthName());
    }
}
