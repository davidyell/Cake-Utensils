<?php
namespace App\Database\Type;

use Cake\Database\Driver;
use Cake\Database\Type;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberUtil;
use PDO;

class TelephoneType extends Type
{
    /**
     * Casts given value from a PHP type to one acceptable by a database.
     *
     * @param mixed $value Value to be converted to a database equivalent.
     * @param \Cake\Database\Driver $driver Object from which database preferences and configuration will be extracted.
     * @return mixed Given PHP type casted to one acceptable by a database.
     */
    public function toDatabase($value, Driver $driver)
    {
        return $value;
    }

    /**
     * Casts given value from a database type to PHP equivalent
     *
     * @param mixed $value Value to be converted to PHP equivalent
     * @param \Cake\Database\Driver $driver Object from which database preferences and configuration will be extracted
     * @return PhoneNumber|null
     * @throws NumberParseException If number cannot be parsed
     */
    public function toPHP($value, Driver $driver): ?PhoneNumber
    {
        $phoneUtil = PhoneNumberUtil::getInstance();

        if ($value === 'outbound' || empty($value)) {
            return null;
        }

        try {
            return $phoneUtil->parse($value, 'GB');
        } catch (NumberParseException $e) {
            throw $e;
        }
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
     * @param mixed $value The value to convert.
     * @return mixed Converted value.
     */
    public function marshal($value)
    {
        return $value;
    }
}
