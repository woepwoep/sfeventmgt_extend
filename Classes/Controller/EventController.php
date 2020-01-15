<?php
namespace RedSeadog\SfeventmgtExtend\Controller;

use \RedSeadog\SfeventmgtExtend\Domain\Model\Event;
use \RedSeadog\SfeventmgtExtend\Domain\Model\Registration;

use \DERHANSEN\SfEventMgt\Utility\MessageType;
use \DERHANSEN\SfEventMgt\Utility\RegistrationResult;

use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
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
     * @param \DERHANSEN\SfEventMgt\Domain\Model\Registration $registration Registration
     * @param \DERHANSEN\SfEventMgt\Domain\Model\Event $event Event
     * @validate $registration \DERHANSEN\SfEventMgt\Validation\Validator\RegistrationFieldValidator
     * @validate $registration \RedSeadog\SfeventmgtExtend\Validation\Validator\RegistrationValidator
     *
     * @return mixed string|void
     */
    public function saveRegistrationAction(
	\DERHANSEN\SfEventMgt\Domain\Model\Registration $registration,
	\DERHANSEN\SfEventMgt\Domain\Model\Event $event
    )
    {
        if (is_a($event, Event::class) && $this->settings['registration']['checkPidOfEventRecord']) {
            $event = $this->checkPidOfEventRecord($event);
        }
        if (is_null($event) && isset($this->settings['event']['errorHandling'])) {
            return $this->handleEventNotFoundError($this->settings);
        }
        $autoConfirmation = (bool)$this->settings['registration']['autoConfirmation'] || $event->getEnableAutoconfirm();
        $result = RegistrationResult::REGISTRATION_SUCCESSFUL;
        list($success, $result) = $this->registrationService->checkRegistrationSuccess($event, $registration, $result);

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
            $registration->_setProperty('_languageUid', $this->getSysLanguageUid());
            $this->registrationRepository->add($registration);

            // Persist registration, so we have an UID
            $this->objectManager->get(PersistenceManager::class)->persistAll();

            // Add new registration (or waitlist registration) to event
            if ($isWaitlistRegistration) {
                $event->addRegistrationWaitlist($registration);
                $messageType = MessageType::REGISTRATION_WAITLIST_NEW;
            } else {
                $event->addRegistration($registration);
                $messageType = MessageType::REGISTRATION_NEW;
            }
            $this->eventRepository->update($event);

            // Fix event in registration for language other than default language
            $this->registrationService->fixRegistrationEvent($registration, $event);

            $this->signalDispatch(__CLASS__, __FUNCTION__ . 'AfterRegistrationSaved', [$registration, $this]);

            // Create given amount of registrations if necessary
            $createDependingRegistrations = $registration->getAmountOfRegistrations() > 1;
            $this->signalDispatch(
                __CLASS__,
                __FUNCTION__ . 'BeforeCreateDependingRegistrations',
                [$registration, &$createDependingRegistrations, $this]
            );
            if ($createDependingRegistrations) {
                $this->registrationService->createDependingRegistrations($registration);
            }

            // Flush page cache for event, since new registration has been added
            $this->eventCacheService->flushEventCache($event->getUid(), $event->getPid());
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
     * Detail view for an event
     *
     * @param $event \DERHANSEN\SfEventMgt\Domain\Model\Event
     */
    public function detailAction(\DERHANSEN\SfEventMgt\Domain\Model\Event $event = null)
    {
		parent::detailAction($event);
    }

}
