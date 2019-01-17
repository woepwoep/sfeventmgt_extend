<?php
namespace RedSeadog\SfeventmgtExtend\Service;

require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('sfeventmgt_extend') . 'Resources/Private/Libraries/dompdf/autoload.inc.php');

/**
 *  PdfService
 */
class PdfService implements \TYPO3\CMS\Core\SingletonInterface
{
	/**
	 * @var \Dompdf\Dompdf
	 */
	protected $dompdf;


	/**
	 * Constructor
	 */
	public function __construct($html)
	{
		$this->dompdf = new \Dompdf\Dompdf();
		$this->dompdf->setPaper('A4', 'portrait');
		$this->dompdf->loadHtml($html);
		$this->dompdf->render();
	}

	/**
	 * @return string;
	 */
	public function output()
	{
		return $this->dompdf->output();
	}
}
