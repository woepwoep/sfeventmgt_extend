<?php
namespace RedSeadog\SfeventmgtExtend\Domain\Repository;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @author Ronald Wopereis <woepwoep@gmail.com>
 */
class RegistrationRepository extends \DERHANSEN\SfEventMgt\Domain\Repository\RegistrationRepository
{
	/**
	 * @return int
	 */
	public function getNextFactuurnr()
	{
		// setup $query
		$query = $this->createQuery();

		// select max(factuurnr)
		$query->setOrderings(
			array(
				'factuurnr' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING,
			)
		);
		$result = $query->execute()->getFirst()->getFactuurnr();

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

	/**
	 * Returns number of registrations for the given event where paid=1
	 *
	 * @param \RedSeadog\SfeventmgtExtend\Domain\Model\Event $event Event
	 *
	 * @return int
	 */
	public function nrOfPaidRegistrations($event)
	{
		// setup $query
		$query = $this->createQuery();

		// search for registrations for this $event where paid=1
		$constraints = [];
		$constraints[] = $query->equals('event',$event);
		$constraints[] = $query->equals('paid',1);
		$query->matching(
			$query->LogicalAnd($constraints)
		);

		// return result
		return $query->execute()->count();
	}
}
