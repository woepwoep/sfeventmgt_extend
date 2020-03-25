<?php
defined('TYPO3_MODE') || die('Access denied.');

use TYPO3\CMS\Core\Utility\GeneralUtility;

call_user_func(
    function () {
        // XCLASS event
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\DERHANSEN\SfEventMgt\Domain\Model\Event::class] = [
            'className' => \RedSeadog\SfeventmgtExtend\Domain\Model\Event::class
        ];

        // Register extended domain class
        GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class)
            ->registerImplementation(
                \DERHANSEN\SfEventMgt\Domain\Model\Event::class,
                \RedSeadog\SfeventmgtExtend\Domain\Model\Event::class
            );

        // XCLASS registration
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\DERHANSEN\SfEventMgt\Domain\Model\Registration::class] = [
            'className' => \RedSeadog\SfeventmgtExtend\Domain\Model\Registration::class
        ];

        // Register extended registration class
        GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\Container\Container::class)
            ->registerImplementation(
                \DERHANSEN\SfEventMgt\Domain\Model\Registration::class,
                \RedSeadog\SfeventmgtExtend\Domain\Model\Registration::class
            );

        // XCLASS EventController
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\DERHANSEN\SfEventMgt\Controller\EventController::class] = [
            'className' => \RedSeadog\SfeventmgtExtend\Controller\EventController::class
        ];
    }
);

/** @var \TYPO3\CMS\Extbase\SignalSlot\Dispatcher $signalSlotDispatcher */
$signalSlotDispatcher = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\SignalSlot\\Dispatcher');
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
