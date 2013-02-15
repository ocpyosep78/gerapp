<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Status_Nikah extends CI_Model {
	function __construct() {
        parent::__construct();
    }
	
	function GetArray($Param) {
		$Array = array(
			0 => array('id' => '1', 'value' => 'Belum Menikah'),
			1 => array('id' => '2', 'value' => 'Sudah Menikah'),
			2 => array('id' => '3', 'value' => 'Janda'),
			3 => array('id' => '4', 'value' => 'Duda')
		);
		return $Array;
	}
}