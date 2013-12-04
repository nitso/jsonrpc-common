<?php

use Moaction\Jsonrpc\Common\Error;
use Moaction\Jsonrpc\Common\Request;
use Moaction\Jsonrpc\Common\Response;

class ResponseTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @dataProvider providerTestWrongVersion
	 * @covers       \Moaction\Jsonrpc\Common\Response::fromArray
	 */
	public function testWrongVersion($version, $expected)
	{
		if (!$expected) {
			$this->setExpectedException(
				'\Moaction\Jsonrpc\Common\Exception',
				'Request is not valid JsonRPC request: missing version'
			);
		}

		$data = array(
			'result' => true,
			'id'     => 1,
		);
		if ($version) {
			$data['jsonrpc'] = $version;
		}

		Response::fromArray($data);
	}

	/**
	 * @return array
	 */
	public function providerTestWrongVersion()
	{
		return array(
			'Empty version' => array('', false),
			'Bad version'   => array('1', false),
			'Good version'  => array(Request::VERSION, true),
		);
	}

	/**
	 * @covers       \Moaction\Jsonrpc\Common\Response::fromArray
	 * @dataProvider providerTestWrongId
	 */
	public function testWrongId($id, $expected)
	{
		$data = array(
			'jsonrpc' => Request::VERSION,
			'result'  => true,
		);
		if ($id) {
			$data['id'] = $id;
		}

		if (!$expected) {
			$this->setExpectedException(
				'\Moaction\Jsonrpc\Common\Exception',
				'Request is not valid JsonRPC request: missing id'
			);
		}

		Response::fromArray($data);
	}

	/**
	 * @return array
	 */
	public function providerTestWrongId()
	{
		return array(
			'Bad id' => array('', false),
			'Good id' => array(10, true),
		);
	}

	/**
	 * @covers \Moaction\Jsonrpc\Common\Response::fromArray
	 */
	public function testEmptyResultAndError()
	{
		$data = array(
			'jsonrpc' => Request::VERSION,
			'id'      => 1
		);

		$this->setExpectedException(
			'\Moaction\Jsonrpc\Common\Exception',
			'Request is not valid JsonRPC request: missing result'
		);
		Response::fromArray($data);
	}

	/**
	 * @covers \Moaction\Jsonrpc\Common\Response::fromArray
	 */
	public function testResult()
	{
		$data = array(
			'jsonrpc' => Request::VERSION,
			'id'      => 5,
			'result'  => array('Hello world'),
		);

		$response = Response::fromArray($data);
		$this->assertEquals(5, $response->getId());
		$this->assertEquals(array('Hello world'), $response->getResult());
	}

	/**
	 * @covers \Moaction\Jsonrpc\Common\Response::fromArray
	 */
	public function testError()
	{
		$data = array(
			'jsonrpc' => Request::VERSION,
			'id'      => 5,
			'error'   => array('123'),
		);

		$error = new Error('123');

		/** @var PHPUnit_Framework_MockObject_MockObject $responseMockClass */
		$responseMockClass = $this->getMockClass('\Moaction\Jsonrpc\Common\Response', array('getErrorObject'));
		$responseMockClass::staticExpects($this->once())
			->method('getErrorObject')
			->with(array('123'))
			->will($this->returnValue($error));

		$expected = new Response();
		$expected->setId(5);
		$expected->setError($error);

		$this->assertEquals($expected, $responseMockClass::fromArray($data));
	}

	/**
	 * @covers \Moaction\Jsonrpc\Common\Response::setError
	 * @covers \Moaction\Jsonrpc\Common\Response::setId
	 * @covers \Moaction\Jsonrpc\Common\Response::setResult
	 * @covers \Moaction\Jsonrpc\Common\Response::getError
	 * @covers \Moaction\Jsonrpc\Common\Response::getId
	 * @covers \Moaction\Jsonrpc\Common\Response::getResult
	 */
	public function testGettersSetters()
	{
		$response = new Response();
		$response->setError(new Error());
		$response->setId(4);
		$response->setResult(array('result'));

		$this->assertEquals(new Error(), $response->getError());
		$this->assertEquals(4, $response->getId());
		$this->assertEquals(array('result'), $response->getResult());
	}

	/**
	 * @dataProvider providerTestHasError
	 * @covers       \Moaction\Jsonrpc\Common\Response::hasError
	 */
	public function testHasError($error)
	{
		$response = new Response();
		if ($error) {
			$response->setError(new Error());
		}
		$this->assertEquals($error, $response->hasError());
	}

	public function providerTestHasError()
	{
		return array(
			'Has error' => array(true),
			'No error'  => array(false)
		);
	}
}