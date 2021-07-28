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
		if (md5($order_id) == $_REQUEST['key']) {
			try {
				$client = new WooppaySoapClient($processor_data['processor_params']['url']);
			} catch (WooppaySoapException $e) {
			}
			$login_request = new CoreLoginRequest();
			$login_request->username = $processor_data['processor_params']['login'];
			$login_request->password = $processor_data['processor_params']['password'];
			try {
				if ($client->login($login_request)) {
					$operation_id = db_query("SELECT `wooppay_transaction_id` FROM `wooppay_order_transaction` `pt`  WHERE `pt`.`order_id` = '" . $order_id . "' LIMIT 1");
					$operation_id = mysqli_fetch_row($operation_id);
					if ($operation_id) {
						$operationdata_request = new CashGetOperationDataRequest();
						$operationdata_request->operationId = array($operation_id[0]);
						$operation_data = $client->getOperationData($operationdata_request);
						if ($operation_data->response->records[0]->status == WooppayOperationStatus::OPERATION_STATUS_DONE) {
							$pp_response = array(
								'order_status' => 'P'
							);
						} else {
							$pp_response = array(
								'order_status' => 'I'
							);
						}
						fn_update_order_payment_info($order_id, $pp_response);
						fn_change_order_status($order_id, $pp_response['order_status'], $status_from = '', $force_notification = array(), $place_order = false);
						header('Content-type: application/json');
						die('{"data:1"}');
					}
				}
			} catch (Exception $exception) {
			}
		}
	}
} else {
	try {
		$client = new WooppaySoapClient($processor_data['processor_params']['url']);
	} catch (WooppaySoapException $e) {
	}
	$login_request = new CoreLoginRequest();
	$login_request->username = $processor_data['processor_params']['login'];
	$login_request->password = $processor_data['processor_params']['password'];
	if ($client->login($login_request)) {
		$invoice_request = new CashCreateInvoiceByServiceRequest();
		$invoice_request->referenceId = $processor_data['processor_params']['prefix'] . $order_id;
		$invoice_request->serviceName = $processor_data['processor_params']['service'];
		$invoice_request->backUrl = fn_url("payment_notification.return?payment=wooppay&order_id=$order_id", AREA,
			'current');
		$invoice_request->requestUrl = fn_url("payment_notification.approve?payment=wooppay&order_id=$order_id&key=" . md5($order_id) . "",
			AREA, 'current');;
		$invoice_request->addInfo = 'Payment for Order ' . $order_id;
		$invoice_request->amount = (float)$order_info['total'];
		$invoice_request->userPhone = $order_info['phone'];
		$invoice_request->userEmail = $order_info['email'];
		$invoice_request->deathDate = '';
		$invoice_request->description = '';
		if (isset($order_info['phone'])) {
			$invoice_request->serviceType = 4;
		} else {
			$invoice_request->serviceType = 0;
		}
		$invoice_data = $client->createInvoice($invoice_request);
		db_query("INSERT INTO `wooppay_order_transaction` SET
			`order_id` = '" . (int)$order_id . "',
			`wooppay_transaction_id` = '" . $invoice_data->response->operationId . "'");
		fn_create_payment_form($invoice_data->response->operationUrl, array(), 'Wooppay', true, 'GET');
	}
}



