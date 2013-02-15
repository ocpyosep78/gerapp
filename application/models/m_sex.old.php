<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Sex extends CI_Model {
	function __construct() {
        parent::__construct();
    }
	
	function GetArray($Param) {
		$Array = array(
			0 => array('id' => 'L', 'value' => 'Laki Laki'),
			1 => array('id' => 'P', 'value' => 'Perempuan')
		);
		return $Array;
	}
}