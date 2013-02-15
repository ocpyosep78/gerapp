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
class pendanaan extends CI_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
        $this->M_User->LoginRequired();
        $this->load->view( 'finance/pendanaan' );
    }
	
    function grid() {
        $this->M_User->LoginRequired();
		
		$Result = $this->M_Pendanaan->GetArray($_POST);
		
		echo json_encode($Result);
    }
	
	function action() {
        $this->M_User->LoginRequired();
		
		$Result = array();
		$Action = (isset($_POST['Action'])) ? $_POST['Action'] : '';
		
		if ($Action == 'UpdatePendanaan') {
			$Result = $this->M_Pendanaan->Update($_POST);
		} else if ($Action == 'GetPendanaanByID') {
			$Result = $this->M_Pendanaan->GetByID(array('pendanaan_id' => $_POST['pendanaan_id']));
		} else if ($Action == 'DetelePendanaanByID') {
			$Result = $this->M_Pendanaan->Delete(array('pendanaan_id' => $_POST['pendanaan_id']));
		}
		
		echo json_encode($Result);
	}
	
	function view() {
		$this->load->view( 'finance/popup/pendanaan');
	}
}