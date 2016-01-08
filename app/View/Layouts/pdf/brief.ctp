<?php
// print_r($content_for_layout);die;
App::import('Vendor','xtcpdf');
header("Content-type: application/pdf");
$pdf = new XTCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Fyooz');
$pdf->SetTitle('Brief');
$pdf->SetSubject('Brief');
$pdf->SetKeywords('Brief, PDF');

// set default header data
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, 'Fyooz CRM', 'by ..');

// set header and footer fonts
$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

$pdf->AddPage();
$pdf->writeHTML($content_for_layout, true, false, true, false, '');
$pdf->lastPage();
echo $pdf->Output('Brief-'. substr(md5(time()),0,3) .'.pdf', 'D'); 