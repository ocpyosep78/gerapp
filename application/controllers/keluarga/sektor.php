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
class sektor extends CI_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
        $this->M_User->LoginRequired();
        $this->load->view( 'keluarga/sektor' );
    }
	
    function grid() {
        $this->M_User->LoginRequired();
		$User = $this->M_User->GetCurrentUser();
		$AdminGerejaID = $this->M_Permission->GetAccessGerejaID($User);
		
		$GerejaID = (isset($_POST['gereja_id'])) ? $_POST['gereja_id'] : 0;
		$GerejaID = (empty($AdminGerejaID)) ? $GerejaID : $AdminGerejaID;
		
		$this->M_Sektor->SetDefault(array('gereja_id' => $GerejaID));
		
		$Param['parent_id'] = 0;
		$Param['gereja_id'] = $GerejaID;
		$Result = $this->M_Sektor->GetArray($Param);
		
		echo json_encode($Result);
    }
	
	function combo() {
        $this->M_User->LoginRequired();
		$User = $this->M_User->GetCurrentUser();
		
		$Param['parent_id'] = 0;
		$Param['gereja_id'] = $this->M_Permission->GetAccessGerejaID($User);
		$Result = $this->M_Sektor->GetArray($Param);
		
		echo json_encode($Result);
	}
	
	function action() {
        $this->M_User->LoginRequired();
		$UserAdmin = $this->session->userdata('UserAdmin');
		$AccessGerejaID = $this->M_Permission->GetAccessGerejaID($UserAdmin);
		
		$Result = array();
		$Action = (isset($_POST['Action'])) ? $_POST['Action'] : '';
		
		if ($Action == 'UpdateSektor') {
			$Result = $this->M_Sektor->Update($_POST);
		} else if ($Action == 'GetSektorByID') {
			$Result = $this->M_Sektor->GetByID(array('sektor_id' => $_POST['sektor_id']));
		} else if ($Action == 'DeteleSektorByID') {
			$Result = $this->M_Sektor->Delete(array('sektor_id' => $_POST['sektor_id']));
		}
		
		echo json_encode($Result);
	}
	
	function view() {
		$this->load->view( 'keluarga/popup/sektor');
	}
}