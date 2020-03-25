<?php
namespace RedSeadog\SfeventmgtExtend\Domain\Model;

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
 * Registration
 *
 * @author Ronald Wopereis <woepwoep@gmail.com>
 */
class Registration extends \DERHANSEN\SfEventMgt\Domain\Model\Registration
{
    /**
     * bignr
     *
     * @var string
     */
    protected $bignr = '';

    /**
     * venvnr
     *
     * @var string
     */
    protected $venvnr = '';

    /**
     * geboorteplaats
     *
     * @var string
     */
    protected $geboorteplaats = '';

    /**
     * functie
     *
     * @var string
     */
    protected $functie = '';
	
    /**
     * factuurnr
     *
     * @var string
     */
    protected $factuurnr = '';

    /**
     * paymentPrice
     *
     * @var float
     */
    protected $paymentPrice = 0.0;

    /**
     * crdate
     *
     * @var int
     */
    protected $crdate;


    /** @return string $bignr */
    public function getBignr()
    {
        return $this->bignr;
    }

    /**
     * @param string $bignr Bignr
     * @return void
     */
    public function setBignr($bignr)
    {
        $this->bignr = $bignr;
    }
	

    /** @return string $venvnr */
    public function getVenvnr()
    {
        return $this->venvnr;
    }

    /**
     * @param string $venvnr
     * @return void
     */
    public function setVenvnr($venvnr)
    {
        $this->venvnr = $venvnr;
    }
	

    /** @return string $geboorteplaats */
    public function getGeboorteplaats()
    {
        return $this->geboorteplaats;
    }

    /**
     * @param string $geboorteplaats
     * @return void
     */
    public function setGeboorteplaats($geboorteplaats)
    {
        $this->geboorteplaats = $geboorteplaats;
    }
	
    /** @return string $functie */
    public function getFunctie()
    {
        return $this->functie;
    }

    /**
     * @param string $functie
     * @return void
     */
    public function setFunctie($functie)
    {
        $this->functie = $functie;
    }
	
	/** @return string $factuurnr */
    public function getFactuurnr()
    {
        return $this->factuurnr;
    }

    /**
     * @param string $factuurnr
     * @return void
     */
    public function setFactuurnr($factuurnr)
    {
        $this->factuurnr = $factuurnr;
    }

    /** @return float $paymentPrice */
    public function getPaymentPrice()
    {
        return $this->paymentPrice;
    }

    /**
     * @param float $paymentPrice
     * @return void
     */
    public function setPaymentPrice($paymentPrice)
    {
        $this->paymentPrice = $paymentPrice;
    }


	/**
	 * if paid then confirmed
	 *
	 * @param boolean $paid
	 * @return void
	 */
	public function setPaid($paid)
	{
		$this->paid = $paid;
		$this->setConfirmed($paid);
	}

	/**
	 * generate invoice name from Factuurnr and RegistrationTimestamp
	 *
	 * @return void
	 */
	public function getFactuurNaam()
	{
		$factuurNaam = $this->getFactuurNr().'-'.$this->getPaymentReference().'.pdf';
		return $factuurNaam;
	}

	/** @return string */
	public function getInvoiceFileName()
	{
		$dirname = 'uploads/tx_sfeventmgtextend/';
		$invoiceFileName = $dirname . $this->getFactuurNaam();
		if (!strlen($invoiceFileName)) {
			debug('getInvoiceFileName: could not get invoiceFileName.');
			exit(1);
		}
		return $invoiceFileName;
	}

	/** @return string */
	public function getAbsInvoicePathAndFileName()
	{
		$absFileName = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName(PATH_site.$this->getInvoiceFileName());
		if (!$absFileName) {
			debug('getAbsInvoicePathAndFileName: absFileName=empty. exiting');
			exit(1);
		}
		return $absFileName;
	}

	/** @return int $crdate */
	public function getCrdate()
	{
		return $this->crdate;
	}
}
