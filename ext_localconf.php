<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

/** @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher */
$signalSlotDispatcher = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher');
$signalSlotDispatcher->connect(
	'DERHANSEN\\SfEventMgt\\Controller\\PaymentController',
	'successActionProcessSuccessIdeal',
	'RedSeadog\\SfeventmgtExtend\\Controller\\PaymentController',
	'followupSuccessAction',
	false
);
$signalSlotDispatcher->connect(
	'DERHANSEN\\SfEventMgt\\Controller\\PaymentController',
	'cancelActionProcessCancelIdeal',
	'RedSeadog\\SfeventmgtExtend\\Controller\\PaymentController',
	'followupCancelAction',
	false
);
$signalSlotDispatcher->connect(
	'DERHANSEN\\SfEventMgt\\Controller\\PaymentController',
	'failureActionProcessFailureIdeal',
	'RedSeadog\\SfeventmgtExtend\\Controller\\PaymentController',
	'followupFailureAction',
	false
);
