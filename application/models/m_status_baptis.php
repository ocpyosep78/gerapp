<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Status_Baptis extends CI_Model {
	function __construct() {
        parent::__construct();
    }
	
	function GetArray($Param) {
		$Array = array(
			0 => array('id' => '0', 'value' => 'Belum'),
			1 => array('id' => '1', 'value' => 'Sudah')
		);
		return $Array;
	}
}