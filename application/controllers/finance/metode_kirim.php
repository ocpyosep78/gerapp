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
class metode_kirim extends CI_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
        $this->M_User->LoginRequired();
        $this->load->view( 'finance/metode_kirim' );
    }
	
    function grid() {
        $this->M_User->LoginRequired();
		
		$Result = $this->M_Metode_Kirim->GetArray($_POST);
		
		echo json_encode($Result);
    }
	
	function action() {
        $this->M_User->LoginRequired();
		
		$Result = array();
		$Action = (isset($_POST['Action'])) ? $_POST['Action'] : '';
		
		if ($Action == 'UpdateMetodeKirim') {
			$Result = $this->M_Metode_Kirim->Update($_POST);
		} else if ($Action == 'GetMetodeKirimByID') {
			$Result = $this->M_Metode_Kirim->GetByID(array('metode_kirim_id' => $_POST['metode_kirim_id']));
		} else if ($Action == 'DeteleMetodeKirimByID') {
			$Result = $this->M_Metode_Kirim->Delete(array('metode_kirim_id' => $_POST['metode_kirim_id']));
		}
		
		echo json_encode($Result);
	}
	
	function view() {
		$this->load->view( 'finance/popup/metode_kirim');
	}
}