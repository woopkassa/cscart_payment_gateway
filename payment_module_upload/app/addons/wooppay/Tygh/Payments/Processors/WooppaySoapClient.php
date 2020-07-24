<?php
/***************************************************************************
 *                                                                          *
 *   (c) 2004 Vladimir V. Kalynyak, Alexey V. Vinokurov, Ilya M. Shalnev    *
 *                                                                          *
 * This  is  commercial  software,  only  users  who have purchased a valid *
 * license  and  accept  to the terms of the  License Agreement can install *
 * and use this program.                                                    *
 *                                                                          *
 ****************************************************************************
 * PLEASE READ THE FULL TEXT  OF THE SOFTWARE  LICENSE   AGREEMENT  IN  THE *
 * "copyright.txt" FILE PROVIDED WITH THIS DISTRIBUTION PACKAGE.            *
 ****************************************************************************/

namespace Tygh\Payments\Processors;


use Exception;
use SoapClient;

class WooppaySoapClient
{

	private $c;

	/**
	 * WooppaySoapClient constructor.
	 * @param $url
	 * @param array $options
	 * @throws WooppaySoapException
	 */
	public function __construct($url, $options = array())
	{
		try {
			$this->c = new SoapClient($url, $options);
		} catch (Exception $e) {
			throw new WooppaySoapException($e->getMessage());
		}
		if (empty($this->c)) {
			throw new WooppaySoapException('Cannot create instance of Soap client');
		}
	}

	/**
	 * @param $method
	 * @param $data
	 * @return WooppaySoapResponse
	 * @throws BadCredentialsException
	 * @throws UnsuccessfulResponseException
	 * @throws WooppaySoapException
	 */
	public function __call($method, $data)
	{
		try {

			$response = $this->c->$method($data[0]);
		} catch (Exception $e) {
			throw new WooppaySoapException($e->getMessage());
		}
		$response = new WooppaySoapResponse($response);
		switch ($response->error_code) {
			case 0:
				return $response;
				break;
			case 5:
				throw new BadCredentialsException();
				break;
			default:
				throw new UnsuccessfulResponseException('Error code ' . $response->error_code);
		}

	}

	/**
	 * @param CoreLoginRequest $data
	 * @return bool
	 */
	public function login(CoreLoginRequest $data)
	{
		$response = $this->core_login($data);

		if (isset($response->response->session)) {
			$this->c->__setCookie('session', $response->response->session);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * @param CashGetOperationDataRequest $data
	 * @return mixed
	 */
	public function getOperationData(CashGetOperationDataRequest $data)
	{
		return $this->cash_getOperationData($data);
	}

	/**
	 * @param CashCreateInvoiceByServiceRequest $data
	 * @return mixed
	 */
	public function createInvoice(CashCreateInvoiceByServiceRequest $data)
	{
		return $this->cash_createInvoiceByService($data);
	}

	/**
	 * @return array
	 */
	public function getLastDialog()
	{
		return array('req' => $this->c->__getLastRequest(), 'res' => $this->c->__getLastResponse());
	}
}

