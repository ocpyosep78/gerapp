<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Hubungan_Keluarga extends CI_Model {
	function __construct() {
        parent::__construct();
    }
	
	function GetArray($Param = array()) {
		$Array = array(
			0 => array('id' => '01', 'value' => 'Kepala Keluarga'),
			1 => array('id' => '02', 'value' => 'Istri'),
			2 => array('id' => '03n', 'value' => 'Anak'),
			3 => array('id' => '04n', 'value' => 'Orang Tua'),
			4 => array('id' => '05n', 'value' => 'Cucu'),
			5 => array('id' => '06n', 'value' => 'Kakak / Adik Kandung'),
			6 => array('id' => '07n', 'value' => 'Menantu'),
			7 => array('id' => '08n', 'value' => 'Family lain')
		);
		return $Array;
	}
	
	function GetHashArray($Param = array()) {
		$Result = array();
		$Array = $this->GetArray($Param);
		foreach ($Array as $Key => $Temp) {
			$Result[$Temp['id']] = $Temp['value'];
		}
		return $Result;
	}
}