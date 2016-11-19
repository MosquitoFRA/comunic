<?php
//Vérification de sécurité
if(!isset($nom_fichier_pdf) OR !isset($liste_pages) OR !isset($auteur_pdf) OR !isset($titre_pdf) OR !isset($sujet_pdf) OR !isset($pdf_key_word))
	die('Invalid call !');

if(!isset($complement_url))
	$complement_url = "./";

// Include the main TCPDF library (search for installation path).
require_once(relativePath_3rdparty('tcpdf/tcpdf.php'));

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($auteur_pdf);
$pdf->SetTitle($titre_pdf);
$pdf->SetSubject($sujet_pdf);
$pdf->SetKeywords($pdf_key_word);

// set default header data
//$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 061', PDF_HEADER_STRING);

// set header and footer fonts
//$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
//$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
	require_once(dirname(__FILE__).'/lang/eng.php');
	$pdf->setLanguageArray($l);
}

// ---------------------------------------------------------

// set font
$pdf->SetFont('helvetica', '', 10);

/* NOTE:
 * *********************************************************
 * You can load external XHTML using :
 *
 * $html = file_get_contents('/path/to/your/file.html');
 *
 * External CSS files will be automatically loaded.
 * Sometimes you need to fix the path of the external CSS.
 * *********************************************************
 */

//Ajout des pages
foreach($liste_pages as $ajouter)
{
	// add a page
	$pdf->AddPage();

	// output the HTML content
	$pdf->writeHTML($ajouter, true, false, true, false, '');
}
// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------

//Close and output PDF document
$pdf->Output($nom_fichier_pdf, 'I');

//============================================================+
// END OF FILE
//============================================================+
