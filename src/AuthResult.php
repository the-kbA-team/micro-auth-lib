<?php

namespace kbATeam\MicroAuthLib;

use kbATeam\MicroAuthLib\Exceptions\InvalidParameterException;
use kbATeam\MicroAuthLib\Traits\ParamAuthName;

/**
 * Class AuthResult
 * Kerberos authentication result (the name).
 */
final class AuthResult
{
    /**
     * Constant defining the variable name Apache2
     * uses to submit the authenticated name.
     */
    public const AUTH_NAME = 'REMOTE_USER';

    use ParamAuthName;

    /**
     * AuthResult constructor.
     * @param string $authName
     */
    public function __construct(string $authName)
    {
        $this->setAuthName($authName);
    }

    /**
     * Read the authentication result from the given input array.
     * @param array $input
     * @return AuthResult
     * @throws InvalidParameterException
     */
    public static function read(array $input): AuthResult
    {
        $name = self::readAuthName($input);
        return new self($name);
    }
}
