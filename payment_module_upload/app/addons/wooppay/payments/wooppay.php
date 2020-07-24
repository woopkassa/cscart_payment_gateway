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

use Tygh\Payments\Processors\CashCreateInvoiceByServiceRequest;
use Tygh\Payments\Processors\CashGetOperationDataRequest;
use Tygh\Payments\Processors\CoreLoginRequest;
use Tygh\Payments\Processors\WooppayOperationStatus;
use Tygh\Payments\Processors\WooppaySoapClient;
use Tygh\Payments\Processors\WooppaySoapException;

if (defined('PAYMENT_NOTIFICATION')) {
	if (!empty($_REQUEST['order_id'])) {
		$order_id = $_REQUEST['order_id'];
		$order_info = fn_get_order_info($order_id);
		$processor_data = fn_get_processor_data($order_info['payment_id']);
	}
	if ($mode == 'return') {
		$pp_response = array(
			'order_status' => 'O'
		);
		fn_finish_payment($order_id, $pp_response);
		fn_order_placement_routines('route', $order_id, false);
	}
	if ($mode == 'approve') {
		try {
			$client = new WooppaySoapClient($processor_data['processor_params']['url']);
		} catch (WooppaySoapException $e) {
		}
		$login_request = new CoreLoginRequest();
		$login_request->username = $processor_data['processor_params']['login'];
		$login_request->password = $processor_data['processor_params']['password'];
		try {
			if ($client->login($login_request)) {
				$invoice_request = new CashCreateInvoiceByServiceRequest();
				$invoice_request->referenceId = $processor_data['processor_params']['prefix'] . $order_id;
				$invoice_request->backUrl = '';
				$invoice_request->serviceName = '';
				$invoice_request->requestUrl = '';
				$invoice_request->addInfo = '';
				$invoice_request->amount = null;
				$invoice_request->deathDate = '';
				$invoice_request->description = '';
				$invoice_request->serviceType = 4;
				$invoice_data = $client->createInvoice($invoice_request);
				$wooppay_operation_id = $invoice_data->response->operationId;
				$operationdata_request = new CashGetOperationDataRequest();
				$operationdata_request->operationId = array($wooppay_operation_id);
				$operation_data = $client->getOperationData($operationdata_request);
				if (empty($order_info['payment_info']['order_status']) || $order_info['payment_info']['order_status'] == 'I'){
					$pp_response = array(
						'order_status' => 'O'
					);
					fn_finish_payment($order_id, $pp_response);
				}
				else {
					if ($operation_data->response->records[0]->status == WooppayOperationStatus::OPERATION_STATUS_DONE) {
						$pp_response = array(
							'order_status' => 'P'
						);
					}
					else {
						$pp_response = array(
							'order_status' => 'I'
						);
					}
					fn_update_order_payment_info($order_id, $pp_response);
					header('Content-type: application/json');
					echo '{"data:1"}';
					exit;
				}
			}
		}catch (Exception $exception){
		}
	}
}
else {
	try {
		$client = new WooppaySoapClient($processor_data['processor_params']['url']);
	} catch (WooppaySoapException $e) {
	}
	$login_request = new CoreLoginRequest();
	$login_request->username = $processor_data['processor_params']['login'];
	$login_request->password = $processor_data['processor_params']['password'];
	if ($client->login($login_request)){
		$invoice_request = new CashCreateInvoiceByServiceRequest();
		$invoice_request->referenceId = $processor_data['processor_params']['prefix'] . $order_id;
		$invoice_request->serviceName = $processor_data['processor_params']['service'];
		$invoice_request->backUrl = fn_url("payment_notification.return?payment=wooppay&order_id=$order_id", AREA, 'current');
		$invoice_request->requestUrl = fn_url("payment_notification.approve?payment=wooppay&order_id=$order_id", AREA, 'current');;
		$invoice_request->addInfo = 'Payment for Order ' . $order_id;
		$invoice_request->amount = (float) $order_info['total'];
		$invoice_request->userPhone = $order_info['phone'];
		$invoice_request->userEmail = $order_info['email'];
		$invoice_request->deathDate = '';
		$invoice_request->description = '';
		$invoice_request->serviceType = 9;
		$invoice_data = $client->createInvoice($invoice_request);
		fn_create_payment_form($invoice_data->response->operationUrl, array(), 'Wooppay', true, 'GET');
	}
}

