<?php

use Moaction\Jsonrpc\Common\Error;

class ErrorTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers \Moaction\Jsonrpc\Common\Error::__construct
	 * @dataProvider providerTestConstruct
	 */
	public function testConstruct($errorCode, $errorMessage, $errorData, $expectedCode)
	{
		$error = new Error($errorCode, $errorMessage,$errorData);

		$object = new \ReflectionObject($error);

		$code = $object->getProperty('code');
		$code->setAccessible(true);
		$this->assertEquals($expectedCode, $code->getValue($error));

		$message = $object->getProperty('message');
		$message->setAccessible(true);
		$this->assertEquals($errorMessage, $message->getValue($error));

		$data = $object->getProperty('data');
		$data->setAccessible(true);
		$this->assertEquals($errorData, $data->getValue($error));
	}

	/**
	 * @return array
	 */
	public function providerTestConstruct()
	{
		return array(
			'Int code'      => array(10, 'Message 1', array('data 1'), 10),
			'Mixed code'    => array('2a', 'Message 2', array('data 2'), 2),
			'Invalid  code' => array('string', 'Message 3', array('data 3'), 0),
		);
	}

	/**
	 * @covers \Moaction\Jsonrpc\Common\Error::fromArray
	 */
	public function testFromArray()
	{
		$error = Error::fromArray(
			array(
				'code'    => 33,
				'message' => 'Fake error',
				'data'    => array('id' => 16),
			)
		);

		$this->assertEquals(33, $error->getCode());
		$this->assertEquals('Fake error', $error->getMessage());
		$this->assertEquals(array('id' => 16), $error->getData());
	}

	/**
	 * @covers \Moaction\Jsonrpc\Common\Error::toArray
	 * @dataProvider providerTestToArray
	 */
	public function testToArray($code, $message, $data, $expected)
	{
		/** @var PHPUnit_Framework_MockObject_MockObject|Error $error */
		$error = $this->getMockBuilder('\Moaction\Jsonrpc\Common\Error')
			->disableOriginalConstructor()
			->setMethods(array('getCode', 'getMessage', 'getData'))
			->getMock();

		$error->expects($this->once())
			->method('getCode')
			->will($this->returnValue($code));

		$error->expects($this->once())
			->method('getMessage')
			->will($this->returnValue($message));

		$error->expects($data ? $this->exactly(2) : $this->once())
			->method('getData')
			->will($this->returnValue($data));

		$this->assertEquals($expected, $error->toArray());
	}

	/**
	 * @return array
	 */
	public function providerTestToArray()
	{
		$full = array(
			'code'    => 101,
			'message' => 'Test error message 1',
			'data'    => array('data 1')
		);

		$noData = array(
			'code'    => 102,
			'message' => 'Test error message 2',
		);

		return array(
			'Full data' => array(101, 'Test error message 1', array('data 1'), $full),
			'No data'   => array(102, 'Test error message 2', null, $noData),
		);
	}

	/**
	 * @covers \Moaction\Jsonrpc\Common\Error::getCode
	 * @dataProvider providerTestGetCode
	 */
	public function testGetCode($code, $expected)
	{
		$error = new Error($code);
		$this->assertEquals($expected, $error->getCode());
	}

	/**
	 * @return array
	 */
	public function providerTestGetCode()
	{
		return array(
			'Default code' => array(null, Error::ERROR_SERVER_ERROR),
			'Set code'     => array(1010, 1010),
		);
	}

	/**
	 * @covers \Moaction\Jsonrpc\Common\Error::getCode
	 */
	public function testDefaultCode()
	{
		$error = new Error();
		$this->assertEquals(100500, $error->getCode(100500));
	}

	/**
	 * @covers \Moaction\Jsonrpc\Common\Error::getMessage
	 * @dataProvider providerTestGetMessage
	 */
	public function testGetMessage($code, $message, $expected)
	{
		/** @var PHPUnit_Framework_MockObject_MockObject|Error $error */
		$error = $this->getMockBuilder('\Moaction\Jsonrpc\Common\Error')
			->setMethods(array('getMessageString'))
			->setConstructorArgs(array($code, $message))
			->getMock();

		$error->expects($message ? $this->never() : $this->once())
			->method('getMessageString')
			->will($this->returnValue($expected));

		$this->assertEquals($expected, $error->getMessage());
	}

	/**
	 * @return array
	 */
	public function providerTestGetMessage()
	{
		return array(
			'User message'    => array(10, 'Message test 1', 'Message test 1'),
			'Default message' => array(20, null, 'Message test 2'),
		);
	}

	/**
	 * @covers \Moaction\Jsonrpc\Common\Error::getMessageString
	 */
	public function testGetMessageString()
	{
		$error = new Error;
		$this->assertNull($error->getMessageString(12345), 'Unexistent message');
		$this->assertNotNull($error->getMessageString(Error::ERROR_SERVER_ERROR), 'Existent message');
	}

	/**
	 * @covers \Moaction\Jsonrpc\Common\Error::getData
	 */
	public function testGetData()
	{
		$error = new Error(null, null, array('data'));
		$this->assertEquals(array('data'), $error->getData());
	}
}