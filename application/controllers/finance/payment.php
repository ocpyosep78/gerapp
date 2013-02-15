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
class payment extends CI_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
        $this->M_User->LoginRequired();
        $this->load->view( 'finance/payment' );
    }
	
    function grid() {
        $this->M_User->LoginRequired();
		$User = $this->M_User->GetCurrentUser();
		
		if (!empty($User['gereja_id'])) {
			$_POST['gereja_id'] = $User['gereja_id'];
		}
		
		$result['success'] = true;
		$result['rows'] = $this->M_Tagihan->GetArray($_POST);
		$result['totalCount'] = $this->M_Tagihan->GetCount($_POST);
        
        json_response($result);
    }
}