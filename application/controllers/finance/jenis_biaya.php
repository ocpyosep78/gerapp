<?php

/*
 * Copyright 2011 by ORCA, Jl. Taman Sulfat 7 No 4, Malang, ID
 * All rights reserved
 * 
 * Written By: Herry
 */

/**
 * User controllers
 *
 * @author Herry
 */
class jenis_biaya extends CI_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
        $this->M_User->LoginRequired();
        $this->load->view( 'finance/jenis_biaya' );
    }
	
    function grid() {
        $this->M_User->LoginRequired();
		
		$Result = $this->M_Jenis_Biaya->GetArray($_POST);
		
		echo json_encode($Result);
    }
	
	function action() {
        $this->M_User->LoginRequired();
		
		$Result = array();
		$Action = (isset($_POST['Action'])) ? $_POST['Action'] : '';
		
		if ($Action == 'UpdateJenisBiaya') {
			$Result = $this->M_Jenis_Biaya->Update($_POST);
		} else if ($Action == 'GetJenisBiayaByID') {
			$Result = $this->M_Jenis_Biaya->GetByID(array('jenis_biaya_id' => $_POST['jenis_biaya_id']));
		} else if ($Action == 'DeteleJenisBiayaByID') {
			$Result = $this->M_Jenis_Biaya->Delete(array('jenis_biaya_id' => $_POST['jenis_biaya_id']));
		}
		
		echo json_encode($Result);
	}
	
	function view() {
		$this->load->view( 'finance/popup/jenis_biaya');
	}
}