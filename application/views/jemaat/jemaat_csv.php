<?php
	$SessionID = (isset($_GET['SessionID'])) ? $_GET['SessionID'] : '';
	
	$StoreParam = $this->session->userdata($SessionID);
	$StoreParam['limit'] = 10000;
	$ArrayJemaat = $this->M_Jemaat->GetArray($StoreParam);
	
	if (count($ArrayJemaat) <= 0) {
		exit;
	}
	
	$ContentCsv = "First Name,Last Name,E-Mail,Mobile,Phone\n";
	foreach ($ArrayJemaat as $Array) {
		// Validation
		if (empty($Array['firstname']) && empty($Array['lastname'])) {
			$Temp = explode(" ", $Array['nama'], 2);
			$Array['firstname'] = (!empty($Temp[0])) ? $Temp[0] : '';
			$Array['lastname'] = (!empty($Temp[1])) ? $Temp[1] : '';
		}
		$Array['lastname'] = (empty($Array['lastname'])) ? ' ' : $Array['lastname'];
		$Array['email'] = (empty($Array['email'])) ? '-' : $Array['email'];
		$Hp = preg_replace('/[^0-9]/i', '', $Array['hp']);
		$Phone = preg_replace('/[^0-9]/i', '', $Array['telpon']);
		
		if (!empty($Array['firstname']) && !empty($Hp) && !empty($Phone)) {
			$ContentCsv .= $Array['firstname'] . ',' . $Array['lastname'] . ',' . $Array['email'] . ',' . $Hp . ',' . $Phone . "\n";
		}
	}
	
	$FileName = date("Ymd_His_") . rand(1000,9999) . '.csv';
	$PathOther = $this->config->item('base_path') . '/images/other';
	
	$PathOtherYear = $PathOther . date("/Y");
	$PathOtherYearMonth = $PathOtherYear . date("/m");
	$PathOtherYearMonthDay = $PathOtherYearMonth . date("/d");
	$PathOtherFinal = $PathOtherYearMonthDay . '/' . $FileName;
	
	@mkdir($PathOtherYear);
	@mkdir($PathOtherYearMonth);
	@mkdir($PathOtherYearMonthDay);
	Write($PathOtherFinal, $ContentCsv);
	
	header('Content-Type: application/csv');
	header('Content-Disposition: attachment; filename=' . $FileName);
	header('Pragma: no-cache');
	readfile($PathOtherFinal);
	exit;
?>