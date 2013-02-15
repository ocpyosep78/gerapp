<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Golongan_Darah extends CI_Model {
	function __construct() {
        parent::__construct();
    }
	
	function GetArray($Param) {
		$Array = array(
			0 => array('id' => 'A', 'value' => 'A'),
			1 => array('id' => 'B', 'value' => 'B'),
			2 => array('id' => 'O', 'value' => 'O'),
			3 => array('id' => 'AB', 'value' => 'AB')
		);
		return $Array;
	}
}