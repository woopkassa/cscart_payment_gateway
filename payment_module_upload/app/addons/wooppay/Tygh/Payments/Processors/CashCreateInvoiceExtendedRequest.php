<?php
namespace Tygh\Payments\Processors;

class CashCreateInvoiceExtendedRequest extends CashCreateInvoiceRequest
{
	/**
	 * @var string $userEmail
	 * @soap
	 */
	public $userEmail = '';
	/**
	 * @var string $userPhone
	 * @soap
	 */
	public $userPhone = '';
}

