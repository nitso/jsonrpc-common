<?php

namespace Moaction\Jsonrpc\Common;

class Request
{
	const VERSION = '2.0';

	/**
	 * @var string
	 */
	private $method;

	/**
	 * @var array
	 */
	private $params;

	/**
	 * @var int
	 */
	private $id;

	/**
	 * @param mixed $id
	 * @return $this
	 */
	public function setId($id)
	{
		$this->id = (int)$id;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @param mixed $method
	 * @return $this
	 */
	public function setMethod($method)
	{
		$this->method = $method;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getMethod()
	{
		return $this->method;
	}

	/**
	 * @param mixed $params
	 * @return $this
	 */
	public function setParams(array $params)
	{
		$this->params = $params;

		return $this;
	}

	/**
	 * @return array
	 */
	public function getParams()
	{
		return $this->params ?: array();
	}

	/**
	 * Serialize into array
	 *
	 * @throws \InvalidArgumentException
	 * @return array
	 */
	public function toArray()
	{
		if (!$this->getMethod()) {
			throw new \InvalidArgumentException('Method must be provided for request');
		}

		$data = array(
			'jsonrpc' => self::VERSION,
			'method'  => $this->getMethod(),
		);

		if ($this->getParams()) {
			$data['params'] = $this->getParams();
		}

		if ($this->getId()) {
			$data['id'] = $this->getId();
		}

		return $data;
	}

	/**
	 * @param array $data
	 * @throws Exception
	 * @return self
	 */
	public static function fromArray(array $data)
	{
		if (empty($data['jsonrpc'])) {
			throw new Exception('Request is not valid JsonRPC request: missing protocol version');
		}

		if ($data['jsonrpc'] != self::VERSION) {
			throw new Exception('Request is not valid JsonRPC request: invalid protocol version');
		}

		if (empty($data['method'])) {
			throw new Exception('Request is not valid JsonRPC request: missing method');
		}

		$request = new Request();
		$request->setMethod($data['method']);

		if (!empty($data['id'])) {
			$request->setId($data['id']);
		}

		if (!empty($data['params'])) {
			$request->setParams($data['params']);
		}

		return $request;
	}
}