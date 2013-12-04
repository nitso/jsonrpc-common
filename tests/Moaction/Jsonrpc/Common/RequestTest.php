<?php

use Moaction\Jsonrpc\Common\Request;

class RequestTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @covers       \Moaction\Jsonrpc\Common\Request::toArray
	 * @dataProvider providerTestToArray
	 */
	public function testToArray(Request $request, $expected)
	{
		if (!$expected) {
			$this->setExpectedException('\InvalidArgumentException');
		}
		$this->assertEquals($expected, $request->toArray());
	}

	/**
	 * @return array
	 */
	public function providerTestToArray()
	{
		$emptyRequest = new Request();

		$someRequest = clone $emptyRequest;
		$someRequest->setMethod('testMethod');

		$fullRequest = clone $someRequest;
		$fullRequest->setId(10);
		$fullRequest->setParams(array('param' => 'value'));

		return array(
			'Missing method' => array($emptyRequest, false),
			'Some request'   => array(
				$someRequest,
				array(
					'jsonrpc' => Request::VERSION,
					'method'  => 'testMethod',
				),
			),
			'Full request'   => array(
				$fullRequest,
				array(
					'jsonrpc' => Request::VERSION,
					'method'  => 'testMethod',
					'id'      => 10,
					'params'  => array('param' => 'value'),
				),
			)
		);
	}

	/**
	 * Dummy version test
	 */
	public function testVersion()
	{
		$this->assertEquals('2.0', Request::VERSION);
	}

	/**
	 * @covers \Moaction\Jsonrpc\Common\Request::setId
	 * @covers \Moaction\Jsonrpc\Common\Request::setParams
	 * @covers \Moaction\Jsonrpc\Common\Request::setMethod
	 * @covers \Moaction\Jsonrpc\Common\Request::getId
	 * @covers \Moaction\Jsonrpc\Common\Request::getParams
	 * @covers \Moaction\Jsonrpc\Common\Request::getMethod
	 */
	public function testGettersSetters()
	{
		$request = new Request();
		$request->setId(10);
		$request->setParams(array('params'));
		$request->setMethod('method');

		$this->assertEquals(10, $request->getId());
		$this->assertEquals(array('params'), $request->getParams());
		$this->assertEquals('method', $request->getMethod());
	}
}