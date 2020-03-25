<?php
namespace RedSeadog\SfeventmgtExtend\Controller;

use \RedSeadog\SfeventmgtExtend\Domain\Model\Registration;
use \RedSeadog\SfeventmgtExtend\Service\PdfService;
use \RedSeadog\SfeventmgtExtend\Service\PluginService;
use \RedSeadog\SfeventmgtExtend\Service\SqlService;

use \DERHANSEN\SfEventMgt\Service\NotificationService;
use \DERHANSEN\SfEventMgt\Utility\MessageType;

use \TYPO3\CMS\Core\Utility\DebugUtility;

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

/**
 * @author Ronald Wopereis <woepwoep@gmail.com>
 */
class PaymentController extends \DERHANSEN\SfEventMgt\Controller\PaymentController
{

    /**
     * registration repository
     *
     * @var \DERHANSEN\SfEventMgt\Domain\Repository\RegistrationRepository
     */
    protected $registrationRepository = null;


    /**
     * DI for registrationRepository
     *
     * @param \DERHANSEN\SfEventMgt\Domain\Repository\RegistrationRepository $registrationRepository
     */
    public function injectRegistrationRepository(
	\DERHANSEN\SfEventMgt\Domain\Repository\RegistrationRepository $registrationRepository)
    {
        $this->registrationRepository = $registrationRepository;
    }


    /**
     * Notification Service
     *
     * @var \DERHANSEN\SfEventMgt\Service\NotificationService
     * @inject
     */
    protected $notificationService = null;


	/**
	 * @param array $values
	 * @param bool $updateRegistration
	 * @param \RedSeadog\SfeventmgtExtend\Domain\Model\Registration $registration
	 * @param array $getVars
	 * @param ActionController $pObj
	 * @return void
	 */
	public function followupSuccessAction(&$values, &$updateRegistration, Registration $registration, $getVars, $pObj)
	{
		//
		$sqlService = new SqlService();
		$nextFactuurnr = $sqlService->getNextFactuurnr();

		$registration->setFactuurnr($nextFactuurnr);
		$registration->setPaid(TRUE);

		//
		$updateRegistration = TRUE;
		$this->registrationRepository->update($registration);

		// generate invoice
		$this->createInvoice($registration);

		// attach invoice
		$this->settings['notification']['registrationConfirmed']['attachments']['user']['fromFiles'] = array(
			$registration->getAbsInvoicePathAndFileName(),
		);

		// send out email with invoice attached
		$this->sendEmail($registration);

		// values to be used in Success.html
		$values['registration'] = $registration;
	}

	/**
	 * @param array $values
	 * @param bool $updateRegistration
	 * @param bool $removeRegistration
	 * @param \RedSeadog\SfeventmgtExtend\Domain\Model\Registration $registration
	 * @param array $getVars
	 * @param ActionController $pObj
	 * @return void
	 */
	public function followupCancelAction(&$values, &$updateRegistration, &$removeRegistration, $registration, $getVars, $pObj)
	{
		//
		$registration->setPaid(FALSE);

		//
		$updateRegistration = TRUE;
		$removeRegistration = FALSE;

		// send out email no attachments
		$this->settings['notification']['registrationConfirmed']['attachments']['user']['fromFiles'] = array();
		//RW 2019-05-09 do not send email on Cancel
		//RW $this->sendEmail($registration);

		// values to be used in Cancel.html
		$values['registration'] = $registration;
	}


	/**
	 * @param array $values
	 * @param bool $updateRegistration
	 * @param bool $removeRegistration
	 * @param \RedSeadog\SfeventmgtExtend\Domain\Model\Registration $registration
	 * @param array $getVars
	 * @param ActionController $pObj
	 * @return void
	 */
	public function followupFailureAction(&$values, &$updateRegistration, &$removeRegistration, $registration, $getVars, $pObj)
	{
		//
		$registration->setPaid(FALSE);

		//
		$updateRegistration = TRUE;
		$removeRegistration = FALSE;

		// send out email
		$this->settings['notification']['registrationConfirmed']['attachments']['user']['fromFiles'] = array();
		//RW 2019-05-09 do not send email on Cancel
		//RW $this->sendEmail($registration);

		// values to be used in Failure.html
		$values['registration'] = $registration;
	}

	/**
	 * @param \RedSeadog\SfeventmgtExtend\Domain\Model\Registration $registration
	 * @return void
	 */
	protected function sendEmail(
		\DERHANSEN\SfEventMgt\Domain\Model\Registration $registration)
	{
		if($registration->getConfirmed()) {
			$messageType = MessageType::REGISTRATION_CONFIRMED;
			if ($registration->getWaitlist()) {
				$messageType = MessageType::REGISTRATION_WAITLIST_CONFIRMED;
			}
		} else {
			$messageType = MessageType::REGISTRATION_CANCELLED;
		}

		// Send notifications to user and admin
		$this->notificationService->sendUserMessage(
			$registration->getEvent(),
			$registration,
			$this->settings,
			$messageType
		);
		$this->notificationService->sendAdminMessage(
			$registration->getEvent(),
			$registration,
			$this->settings,
			$messageType
		);

		// Confirm registrations depending on main registration if necessary
		if ($registration->getAmountOfRegistrations() > 1) {
			$this->registrationService->confirmDependingRegistrations($registration);
		}
	}

	/**
	 * @param \RedSeadog\SfeventmgtExtend\Domain\Model\Registration $registration
	 * @return void
	 */
	protected function joopcreateInvoice(Registration $registration)
	{
		$invoiceFileName = $registration->getAbsInvoicePathAndFileName();
		if (file_exists($invoiceFileName)){
			$message = sprintf('createInvoice: file '.$invoiceFileName.' is er al!\n');
			$GLOBALS['BE_USER']->simplelog($message, 'sfeventmgt_extend', error_get_last());
			exit(1);
		}
		$html = $this->getInvoiceHtml($registration);

		// convert html to pdf and return the output
		/** @var \RedSeadog\SfeventmgtExtend\Service\PdfService */
		$pdfService = new PdfService($html);
		$nrBytesWritten = file_put_contents($invoiceFileName,$pdfService->output());
		if ($nrBytesWritten === FALSE) {
			$message = sprintf('createInvoice: could not write to file '.$invoiceFileName.'\n');
			$GLOBALS['BE_USER']->simplelog($message, 'sfeventmgt_extend', error_get_last());
			exit(1);
		}
	}

	/**
	 * @param \RedSeadog\SfeventmgtExtend\Domain\Model\Registration $registration
	 * @return void
	 */
	protected function createInvoice(Registration $registration)
	{
		$invoiceFileName = $registration->getAbsInvoicePathAndFileName();
		if (file_exists($invoiceFileName)){
			$message = sprintf('createInvoice: file '.$invoiceFileName.' is er al!\n');
			$GLOBALS['BE_USER']->simplelog($message, 'sfeventmgt_extend', error_get_last());
			exit(1);
		}

		/** @var \RedSeadog\SfeventmgtExtend\Service\PluginService */
		$pluginService = new PluginService('tx_sfeventmgt');

		/** @var \TYPO3\CMS\Fluid\View\StandaloneView $view */ 
		$view = $this->objectManager->get('TYPO3\\CMS\\Fluid\\View\\StandaloneView');
		$view->setFormat('html');

		$templateName = 'Factuur.html';
		$controller = 'Payment';
		$view->setTemplatePathAndFilename($pluginService->getTemplatePathAndFilename($controller,$templateName));

		$view->assignMultiple([
			'registration' => $registration,
			'invoiceFilename' => $invoiceFilename,
		]);

		$pdf = $view->render(); //return pdf as string, or simply save to file system

		$nrBytesWritten = file_put_contents($invoiceFileName,$pdf);
		if ($nrBytesWritten === FALSE) {
			$message = sprintf('createInvoice: could not write to file '.$invoiceFileName.'\n');
			$GLOBALS['BE_USER']->simplelog($message, 'sfeventmgt_extend', error_get_last());
			exit(1);
		}
	}
}
