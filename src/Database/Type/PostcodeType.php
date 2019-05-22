<?php
namespace Utensils\Database\Type;

use Cake\Database\Driver;
use Cake\Database\Type;
use PDO;
use VasilDakov\Postcode\Exception\InvalidArgumentException;
use VasilDakov\Postcode\Postcode;

class PostcodeType extends Type
{
    /**
     * Casts given value from a PHP type to one acceptable by a database.
     *
     * @param \VasilDakov\Postcode\Postcode $value Value to be converted to a database equivalent.
     * @param \Cake\Database\Driver $driver Object from which database preferences and configuration will be extracted.
     * @return string Given PHP type casted to one acceptable by a database.
     * @throws \VasilDakov\Postcode\Exception\InvalidArgumentException If the value cannot be cast to database compatible value
     */
    public function toDatabase($value, Driver $driver)
    {
        if (is_string($value)) {
            return $value;
        }

        if (method_exists($value, '__toString')) {
            return (string)$value;
        }

        throw new InvalidArgumentException(sprintf('Could not cast `%s` to database compatible string.', gettype($value)));
    }

    /**
     * Casts given value from a database type to PHP equivalent
     *
     * @param mixed $value Value to be converted to PHP equivalent
     * @param \Cake\Database\Driver $driver Object from which database preferences and configuration will be extracted
     * @return \VasilDakov\Postcode\Postcode
     */
    public function toPHP($value, Driver $driver): Postcode
    {
        return $this->castToPostcode($value);
    }

    /**
     * Casts given value to its Statement equivalent.
     *
     * @param mixed $value Value to be converted to PDO statement.
     * @param \Cake\Database\Driver $driver Object from which database preferences and configuration will be extracted.
     * @return mixed Given value casted to its Statement equivalent.
     */
    public function toStatement($value, Driver $driver)
    {
        if ($value === null) {
            return PDO::PARAM_NULL;
        }

        return PDO::PARAM_STR;
    }

    /**
     * Marshalls flat data into PHP objects.
     *
     * Most useful for converting request data into PHP objects,
     * that make sense for the rest of the ORM/Database layers.
     *
     * @param string $value The value to convert.
     * @return \VasilDakov\Postcode\Postcode Converted value.
     */
    public function marshal($value)
    {
        return $this->castToPostcode($value);
    }

    /**
     * Cast a string postcode value into a Postcode value object
     *
     * @param string $postcode
     * @return \VasilDakov\Postcode\Postcode
     * @throws InvalidArgumentException If the postcode is invalid
     */
    private function castToPostcode(string $postcode): Postcode
    {
        try {
            return new Postcode($postcode);
        } catch (InvalidArgumentException $exception) {
            throw $exception;
        }
    }
}
