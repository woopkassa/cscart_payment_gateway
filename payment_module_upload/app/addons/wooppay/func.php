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

if (!defined('BOOTSTRAP')) { die('Access denied'); }

function fn_rus_wooppay_install()
{
	fn_rus_wooppay_uninstall();

	$_data = array(
		'processor' => 'Wooppay',
		'processor_script' => 'wooppay.php',
		'processor_template' => 'views/orders/components/payments/cc_outside.tpl',
		'admin_template' => 'wooppay.tpl',
		'callback' => 'N',
		'type' => 'P',
		'addon' => 'wooppay'
	);

	db_query("INSERT INTO ?:payment_processors ?e", $_data);
	db_query("
			CREATE TABLE IF NOT EXISTS `wooppay_order_transaction` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `order_id` int(11) NOT NULL,
			  `wooppay_transaction_id`  CHAR(20) NOT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT COLLATE=utf8_general_ci;");
}

function fn_rus_wooppay_uninstall()
{
	db_query("DELETE FROM ?:payment_processors WHERE processor_script = ?s", "wooppay.php");
	db_query("DROP TABLE IF EXISTS `wooppay_order_transaction`;");
}

