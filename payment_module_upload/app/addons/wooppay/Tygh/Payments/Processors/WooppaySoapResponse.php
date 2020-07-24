<?php
namespace Tygh\Payments\Processors;

class WooppaySoapResponse
{

	public $error_code;
	public $response;

	/**
	 * WooppaySoapResponse constructor.
	 * @param $response
	 * @throws BadResponseException
	 */
	public function __construct($response)
	{

		if (!is_object($response)) {
			throw new BadResponseException('Response is not an object');
		}

		if (!isset($response->error_code)) {
			throw new BadResponseException('Response do not contains error code');
		}
		$this->error_code = $response->error_code;

		if (!property_exists($response, 'response')) {
			throw new BadResponseException('Response do not contains response body');
		}
		$this->response = $response->response;
	}
}

