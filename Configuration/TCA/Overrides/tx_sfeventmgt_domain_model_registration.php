<?php
/**
 * Add extra properties (fields) to the Registration class
 */
defined('TYPO3_MODE') or die();

$fields = [
	'bignr' => [
		'exclude' => 0,
		'label' => 'LLL:EXT:sfeventmgt_extend/Resources/Private/Language/locallang_db.xlf:tx_sfeventmgtextend_domain_model_registration.bignr',
		'config' => [
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim',
		]
	],
	'venvnr' => [
		'exclude' => 0,
		'label' => 'LLL:EXT:sfeventmgt_extend/Resources/Private/Language/locallang_db.xlf:tx_sfeventmgtextend_domain_model_registration.venvnr',
		'config' => [
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim',
		]
	],
	'geboorteplaats' => [
		'exclude' => 0,
		'label' => 'LLL:EXT:sfeventmgt_extend/Resources/Private/Language/locallang_db.xlf:tx_sfeventmgtextend_domain_model_registration.geboorteplaats',
		'config' => [
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim',
		]
	],
	'functie' => [
		'exclude' => 0,
		'label' => 'LLL:EXT:sfeventmgt_extend/Resources/Private/Language/locallang_db.xlf:tx_sfeventmgtextend_domain_model_registration.functie',
		'config' => [
			'type' => 'input',
			'size' => 30,
			'eval' => 'trim',
		]
	],
	'factuurnr' => [
		'exclude' => 0,
		'label' => 'LLL:EXT:sfeventmgt_extend/Resources/Private/Language/locallang_db.xlf:tx_sfeventmgtextend_domain_model_registration.factuurnr',
		'config' => [
			'type' => 'input',
			'size' => 11,
			'eval' => 'int',
		]
	],
	'payment_price' => [
		'exclude' => 1,
		'label' => 'LLL:EXT:sf_event_mgt/Resources/Private/Language/locallang_db.xlf:tx_sfeventmgt_domain_model_registration.payment_price',
		'config' => [
			'type' => 'input',
			'size' => 5,
			'eval' => 'double2'
		]
	],
];

// add field to tca
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('tx_sfeventmgt_domain_model_registration', $fields);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('tx_sfeventmgt_domain_model_registration','bignr,venvnr,geboorteplaats,functie,factuurnr,payment_price','','after:email');
