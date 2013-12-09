<?php

namespace Moaction\Jsonrpc\Common;

class Error
{
	/**
	 * Protocol error codes
	 */
	const ERROR_PARSE_ERROR      = -32700;
	const ERROR_INTERNAL_ERROR   = -32603;
	const ERROR_INVALID_PARAMS   = -32602;
	const ERROR_METHOD_NOT_FOUND = -32601;
	const ERROR_INVALID_REQUEST  = -32600;
	const ERROR_SERVER_ERROR     = -32000;

	/**
	 * Protocol error messages
	 *
	 * @var array
	 */
	private $errorMessages = array(
		self::ERROR_INTERNAL_ERROR   => 'Internal error',
		self::ERROR_INVALID_PARAMS   => 'Invalid params',
		self::ERROR_INVALID_REQUEST  => 'Invalid Request',
		self::ERROR_METHOD_NOT_FOUND => 'Method not found',
		self::ERROR_PARSE_ERROR      => 'Parse error',
		self::ERROR_SERVER_ERROR     => 'Server error',
	);

	/**
	 * @var int
	 */
	private $code;

	/**
	 * @var string
	 */
	private $message;

	/**
	 * @var mixed
	 */
	private $data;

	/**
	 * @param int $code
	 * @param string $message
	 * @param mixed $data
	 */
	public function __construct($code = null, $message = null, $data = null)
	{
		$this->code = $code;
		$this->message = $message;
		$this->data = $data;
	}

	/**
	 * Get error code
	 *
	 * @param int $defaultCode default return code if not set
	 * @return int
	 */
	public function getCode($defaultCode = self::ERROR_SERVER_ERROR)
	{
		return $this->code ?: $defaultCode;
	}

	/**
	 * @return mixed
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * Get error message
	 *
	 * @return string
	 */
	public function getMessage()
	{
		if (!$this->message) {
			return $this->getMessageString($this->getCode());
		}

		return $this->message;
	}

	/**
	 * Create object from array
	 *
	 * @param array $data
	 * @return self
	 */
	public static function fromArray(array $data)
	{
		return new self(
			!empty($data['code'])    ? (int)$data['code'] : null,
			!empty($data['message']) ? $data['message']   : null,
			!empty($data['data'])    ? $data['data']      : null
		);
	}

	/**
	 * Transform object to array
	 *
	 * @return array
	 */
	public function toArray()
	{
		$error = array(
			'code'    => $this->getCode(),
			'message' => $this->getMessage(),
		);

		if ($this->getData()) {
			$error['data'] = $this->getData();
		}

		return $error;
	}

	/**
	 * Return message string by code
	 *
	 * @param $errorCode
	 * @return string|null
	 */
	public function getMessageString($errorCode)
	{
		return isset($this->errorMessages[$errorCode]) ? $this->errorMessages[$errorCode] : null;
	}
} 