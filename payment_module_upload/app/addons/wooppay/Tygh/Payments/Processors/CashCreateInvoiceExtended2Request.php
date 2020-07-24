<?php
namespace Tygh\Payments\Processors;

class CashCreateInvoiceExtended2Request extends CashCreateInvoiceExtendedRequest
{
	/**
	 * @var int $cardForbidden
	 * @soap
	 */
	public $cardForbidden;
}

