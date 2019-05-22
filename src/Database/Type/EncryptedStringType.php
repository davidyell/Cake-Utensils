<?php
namespace App\Database\Type;

use Cake\Core\Configure;
use Cake\Database\Driver;
use Cake\Database\Type;
use Cake\Database\TypeInterface;
use Cake\Database\Type\OptionalConvertInterface;
use Cake\Utility\Security;
use InvalidArgumentException;
use PDO;

/**
 * String type converter.
 *
 * Use to convert string data between PHP and the database types.
 */
class EncryptedStringType extends Type implements OptionalConvertInterface, TypeInterface
{
    /**
     * Encryption 256bit key
     *
     * @var string
     */
    private $key;

    /**
     * EncryptedStringType constructor.
     *
     * @param string|null $name Name for this type
     */
    public function __construct(?string $name = null)
    {
        parent::__construct($name);

        $this->key = Configure::read('encryption.key');
    }

    /**
     * Encrypt a string value
     *
     * @param string $value Input string
     * @return string Encrypted value
     */
    private function encrypt(string $value)
    {
        $binaryEncrypted = Security::encrypt($value, $this->key);

        return bin2hex($binaryEncrypted);
    }

    /**
     * Convert string data into the database format.
     *
     * @param mixed $value The value to convert.
     * @param \Cake\Database\Driver $driver The driver instance to convert with.
     * @return string|null
     */
    public function toDatabase($value, Driver $driver)
    {
        if (is_string($value)) {
            return $this->encrypt($value);
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            return $this->encrypt($value->__toString());
        }

        if (is_scalar($value)) {
            return $this->encrypt((string)$value);
        }

        throw new InvalidArgumentException(sprintf(
            'Cannot convert value of type `%s` to string',
            getTypeName($value)
        ));
    }

    /**
     * Convert encrypted string values to PHP strings, or return the string if there is a problem
     *
     * @param mixed $value The value to convert.
     * @param \Cake\Database\Driver $driver The driver instance to convert with.
     * @return string|null
     */
    public function toPHP($value, Driver $driver)
    {
        if ($value === null || $value === '') {
            return null;
        }

        // If the value is not already encrypted, just return it
        if (ctype_xdigit($value) === false) {
            return $value;
        }

        try {
            return (string)Security::decrypt(hex2bin($value), $this->key);
        } catch (\Exception $exception) {
            return $value;
        }
    }

    /**
     * Get the correct PDO binding type for string data.
     *
     * @param mixed $value The value being bound.
     * @param \Cake\Database\Driver $driver The driver.
     * @return int
     */
    public function toStatement($value, Driver $driver)
    {
        return PDO::PARAM_STR;
    }

    /**
     * Marshalls request data into PHP strings.
     *
     * @param mixed $value The value to convert.
     * @return string|null Converted value.
     */
    public function marshal($value)
    {
        if ($value === null) {
            return null;
        }
        if (is_array($value)) {
            return '';
        }

        return (string)$value;
    }

    /**
     * Force casting database strings to PHP, so they get decrypted
     *
     * @return bool
     */
    public function requiresToPhpCast()
    {
        return true;
    }
}
