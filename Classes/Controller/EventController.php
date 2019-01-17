<?php
namespace RedSeadog\SfeventmgtExtend\Controller;

use \RedSeadog\SfeventmgtExtend\Domain\Model\Event;
use \RedSeadog\SfeventmgtExtend\Domain\Model\Registration;

use \DERHANSEN\SfEventMgt\Utility\MessageType;
use \DERHANSEN\SfEventMgt\Utility\RegistrationResult;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2017 Ronald Wopereis <woepwoep@gmail.com>
 *
 *  All rights reserved
 *
 ***************************************************************/
 
/**
 * EventController
 */
class EventController extends \DERHANSEN\SfEventMgt\Controller\EventController
{

    /**
     * Saves the registration
     *
     * @param \RedSeadog\SfeventmgtExtend\Domain\Model\Registration $registration Registration
     * @param \RedSeadog\SfeventmgtExtend\Domain\Model\Event $event Event
     * @validate $registration \RedSeadog\SfeventmgtExtend\Validation\Validator\RegistrationValidator
     *
     * @return void
     */
    public function saveRegistrationAction(Registration $registration, Event $event)
    {
        $autoConfirmation = (bool)$this->settings['registration']['autoConfirmation'] || $event->getEnableAutoconfirm();
        $result = RegistrationResult::REGISTRATION_SUCCESSFUL;
        $success = $this->registrationService->checkRegistrationSuccess($event, $registration, $result);

        // Save registration if no errors
        if ($success) {
            $isWaitlistRegistration = $this->registrationService->isWaitlistRegistration(
                $event,
                $registration->getAmountOfRegistrations()
            );
            $linkValidity = (int)$this->settings['confirmation']['linkValidity'];
            if ($linkValidity === 0) {
                // Use 3600 seconds as default value if not set
                $linkValidity = 3600;
            }
            $confirmationUntil = new \DateTime();
            $confirmationUntil->add(new \DateInterval('PT' . $linkValidity . 'S'));

            $registration->setEvent($event);
            $registration->setPid($event->getPid());
            $registration->setConfirmationUntil($confirmationUntil);
            $registration->setLanguage($GLOBALS['TSFE']->config['config']['language']);
            $registration->setFeUser($this->registrationService->getCurrentFeUserObject());
            $registration->setWaitlist($isWaitlistRegistration);
            $registration->_setProperty('_languageUid', $GLOBALS['TSFE']->sys_language_uid);
            $this->registrationRepository->add($registration);

            // Persist registration, so we have an UID
            $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\PersistenceManager')->persistAll();

            // Add new registration (or waitlist registration) to event
            if ($isWaitlistRegistration) {
                $event->addRegistrationWaitlist($registration);
                $messageType = MessageType::REGISTRATION_WAITLIST_NEW;
            } else {
                $event->addRegistration($registration);
                $messageType = MessageType::REGISTRATION_NEW;
            }
            $this->eventRepository->update($event);

            // Create given amount of registrations if necessary
            if ($registration->getAmountOfRegistrations() > 1) {
                $this->registrationService->createDependingRegistrations($registration);
            }

            // Clear cache for configured pages
            $this->utilityService->clearCacheForConfiguredUids($this->settings);
        }

        // Redirect to payment provider if payment/redirect is enabled
        $paymentPid = (int)$this->settings['paymentPid'];
        if ($success && $paymentPid > 0 && $this->registrationService->redirectPaymentEnabled($registration)) {
            $this->uriBuilder->reset()
                ->setTargetPageUid($paymentPid)
                ->setUseCacheHash(false);
            $uri =  $this->uriBuilder->uriFor(
                'redirect',
                [
                    'registration' => $registration,
                    'hmac' => $this->hashService->generateHmac('redirectAction-' . $registration->getUid())
                ],
                'Payment',
                'sfeventmgt',
                'Pipayment'
            );
            $this->redirectToUri($uri);
        }

		// if no payment redirect 
        if ($autoConfirmation && $success) {
            $this->redirect(
                'confirmRegistration',
                null,
                null,
                [
                    'reguid' => $registration->getUid(),
                    'hmac' => $this->hashService->generateHmac('reg-' . $registration->getUid())
                ]
            );
        } else {
            $this->redirect(
                'saveRegistrationResult',
                null,
                null,
                [
                    'result' => $result,
                    'eventuid' => $event->getUid(),
                    'hmac' => $this->hashService->generateHmac('event-' . $event->getUid())
                ]
            );
        }
    }

    /**
     * Confirms the registration if possible and sends e-mails to admin and user
     *
     * @param \RedSeadog\SfeventmgtExtend\Domain\Model\Registration $registration Registration
     * @param string $hmac HMAC for parameters
     *
     * @return void
     */
    public function confirmRegistrationAction($registration, $hmac)
    {
		debug('confirmRegistration in extend. should not be here. exiting'); exit(1);
    }

    /**
     * Detail view for an event
     *
     * @param \RedSeadog\SfeventmgtExtend\Domain\Model\Event $event Event
     */
    public function detailAction(Event $event = null)
    {
		parent::detailAction($event);
    }

}
