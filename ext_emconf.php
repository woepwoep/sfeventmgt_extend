<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "sfeventmgt_extend".
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
	'title' => 'MGZT - Scholingen',
	'description' => 'Uitbreiding sf_event_mgt velden ten behoeve van MGZT',
	'category' => 'fe',
	'author' => 'Ronald Wopereis',
	'author_email' => 'woepwoep@gmail.com',
	'state' => 'stable',
	'uploadfolder' => true,
	'createDirs' => 'uploads/tx_sfeventmgtextend/i',
	'clearCacheOnLoad' => 1,
	'version' => '2.10.01',
	'constraints' => array (
		'depends' => array (
			'sf_event_mgt' => '4.1.2-4.99.99',
		),
		'conflicts' => array (
		),
		'suggests' => array (
		),
	),
	'clearcacheonload' => true,
	'author_company' => 'Red-Seadog',
);

