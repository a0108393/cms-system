<?php
$this->PhpExcel->createWorksheet(); 
$this->PhpExcel->setDefaultFont('Calibri', 12); 

// define table cells 
$table = array( 
    array('label' => __('Description'), 'filter' => true),
	array('label' => __('Destination Url'), 'filter' => true),
	array('label' => __('Generated Url')),
	array('label' => __('Visits'), 'width' => 50, 'wrap' => true)
); 

// heading 
$this->PhpExcel->addTableHeader($table, array('name' => 'Cambria', 'bold' => true)); 

// data 
foreach ($data as $d) { 
    $this->PhpExcel->addTableRow(array( 
        $d['AdvertisingLink']['description'],
		$d['AdvertisingLink']['destination_url'],
		$d['AdvertisingLink']['generated_url'],
		$d['AdvertisingLink']['visits']
    )); 
} 

$this->PhpExcel->addTableFooter(); 
$this->PhpExcel->output(); 