<?php
namespace Utensils\Test\TestCase\Database\Type;

use Cake\Database\Driver;
use Cake\Database\TypeInterface;
use Cake\TestSuite\TestCase;
use PDO;
use Utensils\Database\Type\PostcodeType;
use VasilDakov\Postcode\Postcode;

class PostcodeTypeTest extends TestCase
{
    /**
     * @var Driver
     */
    private $driver;

    /**
     * @var TypeInterface
     */
    private $type;

    /**
     * Setup the test
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->driver = $this->getMockBuilder(Driver\Sqlite::class)
            ->getMock();

        $this->type = new PostcodeType();
    }

    /**
     * Provide postcodes for the test-case
     *
     * @return array
     */
    public function toPHPProvider()
    {
        return [
            ['sp110qn'],
            ['SP110QN'],
            ['sp11 0qn'],
            ['XM4 5HQ'],
            ['W1A 1AA'],
            ['W1U 4DJ']
        ];
    }

    /**
     * @dataProvider toPHPProvider
     * @param string $postcode
     */
    public function testToPHP(string $postcode)
    {
        $result = $this->type->toPHP($postcode, $this->driver);
        $expected = new Postcode($postcode);

        $this->assertEquals($expected, $result);
    }

    public function testToPHPWithInvalidPostcode()
    {
        $this->expectException(\VasilDakov\Postcode\Exception\InvalidArgumentException::class);

        new Postcode('Invalid postcode');
    }

    public function testToStatement()
    {
        $result = $this->type->toStatement('sp110qn', $this->driver);
        $this->assertEquals(PDO::PARAM_STR, $result);

        $result = $this->type->toStatement(null, $this->driver);
        $this->assertEquals(PDO::PARAM_NULL, $result);
    }

    public function testToDatabase()
    {
        $result = $this->type->toDatabase('sp110qn', $this->driver);
        $this->assertEquals('sp110qn', $result);

        $result = $this->type->toDatabase(new Postcode('sp110qn'), $this->driver);
        $this->assertEquals(new Postcode('sp110qn'), $result);
    }

    public function testToDatabaseWithUnstringableValue()
    {
        $this->expectException(\VasilDakov\Postcode\Exception\InvalidArgumentException::class);

        $this->type->toDatabase(['an' => 'array'], $this->driver);
    }

    public function testMarshal()
    {
        $result = $this->type->marshal('sp110qn');
        $expected = new Postcode('sp110qn');

        $this->assertEquals($expected, $result);
    }

    public function testMarshalWithInvalidPostcode()
    {
        $this->expectException(\VasilDakov\Postcode\Exception\InvalidArgumentException::class);

        $this->type->marshal('Something which isnt a valid postcode');
    }
}
