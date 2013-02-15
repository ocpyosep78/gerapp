<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Pekerjaan extends CI_Model {
	function __construct() {
        parent::__construct();
    }
	
	function GetArray($Param) {
		$Array = array(
			'Pedagang' => 'Pedagang',
			'Pegawai Negeri' => 'Pegawai Negeri',
			'Petani' => 'Petani',
			'Peternak' => 'Peternak',
			'Swasta' => 'Swasta'
		);
		return $Array;
	}
}