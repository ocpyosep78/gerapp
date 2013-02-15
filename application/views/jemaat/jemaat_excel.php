<?php
	$SessionID = (isset($_GET['SessionID'])) ? $_GET['SessionID'] : '';
	
	$StoreParam = $this->session->userdata($SessionID);
	$StoreParam['limit'] = 10000;
	$ArrayJemaat = $this->M_Jemaat->GetArray($StoreParam);
	
	$ExcelParam = array(
		'title' => 'Excel 1',
		'ArrayRecord' => array(1, 2, 3)
	);
	
	$ExcelParam['ArrayRecord'] = array();
	foreach ($ArrayJemaat as $Array) {
		$Row = array( $Array['nama'], $Array['nomor'], $Array['tempatlahir'], $Array['tgllahir'], $Array['GerejaNama'] );
		$ExcelParam['ArrayRecord'][] = $Row;
	}
	
	$ExcelResult = new Excel($ExcelParam);
?>