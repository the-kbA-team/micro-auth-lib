<?php

namespace kbATeam\MicroAuthLib\Traits;

use kbATeam\MicroAuthLib\Exceptions\InvalidParameterException;

/**
 * Trait ParamId
 * Request and Response ID parameter.
 */
trait ParamId
{
    /**
     * @var int
     */
    private $id;

    /**
     * Get the ID of this request.
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set the ID for this request or generate a random id.
     * @param int|null $id
     */
    public function setId(int $id = null): void
    {
        if ($id === null) {
            $id = rand(10000, 99999);
        }
        $this->id = $id;
    }

    /**
     * Read the id parameter from the given input array.
     * @param array<string, string> $input
     * @return int
     * @throws InvalidParameterException
     */
    private static function readId(array $input): int
    {
        if (!array_key_exists(self::ID, $input)) {
            throw new InvalidParameterException('ID is missing.');
        }
        $id = filter_var(
            $input[self::ID],
            FILTER_SANITIZE_NUMBER_INT
        );
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            throw new InvalidParameterException('ID is not an integer.');
        }
        return (int)$id;
    }
}
