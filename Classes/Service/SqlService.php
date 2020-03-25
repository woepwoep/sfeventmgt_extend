<?php
namespace RedSeadog\SfeventmgtExtend\Service;

use TYPO3\CMS\Core\Utility\DebugUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 *  SqlService
 */
class SqlService
{
    public function getNextFactuurnr()
    {
	$queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
	    ->getQueryBuilderForTable('tx_sfeventmgt_domain_model_registration');
	$result = $queryBuilder
	    ->select('factuurnr')
	    ->from('tx_sfeventmgt_domain_model_registration')
	    ->orderBy('factuurnr','DESC')
	    ->execute()
	    ->fetchColumn(0);

	// format of the invoicenumber (a string)
	$format = "i-%04d%03d";
	list($year,$seq) = sscanf($result, $format);

	// reset $seq upon new year or firstrun
	$thisyear = date("Y");
	if ($thisyear > $year) {
		$year = $thisyear;
		$seq = 0;
	}

	// build nextnum invoicenumber
	$seq++;
	$factuurnr = sprintf($format,$year,$seq);

	// return result
	return $factuurnr;
    }
}
