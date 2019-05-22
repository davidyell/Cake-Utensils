<?php
namespace Utensils\Test\TestCase\Database\Type;

use Cake\Database\Driver;
use Cake\Database\DriverInterface;
use Cake\Database\TypeInterface;
use Cake\TestSuite\TestCase;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberUtil;
use PDO;
use Utensils\Database\Type\TelephoneType;

class TelephoneTypeTest extends TestCase
{
    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * @var TypeInterface
     */
    private $type;

    public function setUp(): void
    {
        parent::setUp();

        $this->driver = $this->getMockBuilder(Driver\Sqlite::class)->getMock();
        $this->type = new TelephoneType();
    }

    public function testToDatabase()
    {
        $value = '01632 960326';
        $result = $this->type->toDatabase($value, $this->driver);

        $this->assertEquals($value, $result);
    }

    public function testToDatabaseWithPhoneNumberInstance()
    {
        $number = '01632 960326';

        $phoneUtil = PhoneNumberUtil::getInstance();
        $phoneNumber = $phoneUtil->parse($number, 'GB');

        $result = $this->type->toDatabase($phoneNumber, $this->driver);

        $this->assertEquals('Country Code: 44 National Number: 1632960326', $result);
    }

    public function testToDatabaseWithInvalidPhoneNumber()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->type->toDatabase(['an' => 'array'], $this->driver);
    }

    public function providerPhoneNumbers()
    {
        return [
            ['01632 960326', 'Country Code: 44 National Number: 1632960326'],
            ['01632 960465', 'Country Code: 44 National Number: 1632960465']
        ];
    }

    /**
     * @dataProvider providerPhoneNumbers
     * @param string $value The phone number
     * @param $expected
     */
    public function testToPHP($value, $expected)
    {
        $result = $this->type->toPHP($value, $this->driver);

        $this->assertEquals($expected, $result);
    }

    public function testToPHPWithOutboundAndNull()
    {
        $result = $this->type->toPHP('outbound', $this->driver);
        $this->assertEquals(null, $result);

        $result = $this->type->toPHP(null, $this->driver);
        $this->assertEquals(null, $result);
    }

    public function testToPHPWithInvalidPhone()
    {
        $this->expectException(\libphonenumber\NumberParseException::class);

        $this->type->toPHP('Not a valid telephone number', $this->driver);
    }

    public function testToStatement()
    {
        $result = $this->type->toStatement('01632 960326', $this->driver);
        $this->assertEquals(PDO::PARAM_STR, $result);

        $result = $this->type->toStatement(null, $this->driver);
        $this->assertEquals(PDO::PARAM_NULL, $result);
    }

    public function testMarshal()
    {
        $result = $this->type->marshal('01632 960326');
        $this->assertInstanceOf(PhoneNumber::class, $result);
    }
}
