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
	'category' => 'plugin',
	'author' => 'Ronald Wopereis',
	'author_email' => 'woepwoep@gmail.com',
	'state' => 'stable',
	'uploadfolder' => true,
	'createDirs' => 'uploads/tx_sfeventmgtextend/i',
	'clearCacheOnLoad' => 1,
	'version' => '3.01.01',
	'constraints' => [
		'depends' => [
			'sf_event_mgt' => '4.1.2-4.99.99',
		],
		'conflicts' => [],
		'suggests' => [],
	],
	'clearcacheonload' => true,
	'author_company' => 'Red-Seadog',
);

