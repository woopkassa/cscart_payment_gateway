<?php
namespace Tygh\Payments\Processors;

class WooppayOperationStatus
{
	/**
	 * Новая
	 */
	const OPERATION_STATUS_NEW = 1;
	/**
	 * На рассмотрении
	 */
	const OPERATION_STATUS_CONSIDER = 2;
	/**
	 * Отклонена
	 */
	const OPERATION_STATUS_REJECTED = 3;
	/**
	 * Проведена
	 */
	const OPERATION_STATUS_DONE = 4;
	/**
	 * Сторнирована
	 */
	const OPERATION_STATUS_CANCELED = 5;
	/**
	 * Сторнирующая
	 */
	const OPERATION_STATUS_CANCELING = 6;
	/**
	 * Удалена
	 */
	const OPERATION_STATUS_DELETED = 7;
	/**
	 * На квитовании
	 */
	const OPERATION_STATUS_KVITOVANIE = 4;
	/**
	 * На ожидании подверждения или отказа мерчанта
	 */
	const OPERATION_STATUS_WAITING = 9;
}

