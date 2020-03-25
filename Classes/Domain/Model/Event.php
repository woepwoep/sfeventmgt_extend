<?php
namespace RedSeadog\SfeventmgtExtend\Domain\Model;

use \TYPO3\CMS\Core\Utility\GeneralUtility;

/*
 * This file is part of the Extension "sfeventmgt_extend" for TYPO3 CMS.
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

/**
 * Event
 *
 * @author Ronald Wopereis <woepwoep@gmail.com>
 */
class Event extends \DERHANSEN\SfEventMgt\Domain\Model\Event
{
	/**
	 * overwrite calculation of free places
	 *
	 * @return int
	 */
	public function getFreePlaces()
	{
		//return $this->maxParticipants - $this->getRegistration()->count();
		return $this->maxParticipants - $this->getNrOfPaidRegistrations();
	}

	/**
	 * return the number of paid registrations
	 *
	 * @return int
	 */
	public function getNrOfPaidRegistrations()
	{
		$objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		$registrationRepository = $objectManager->get('RedSeadog\SfeventmgtExtend\Domain\Repository\RegistrationRepository');
		return $registrationRepository->nrOfPaidRegistrations($this->uid);
	}
}
