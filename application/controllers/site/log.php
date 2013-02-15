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
class log extends CI_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
        $this->M_User->LoginRequired();
        $this->load->view( 'site/log' );
    }
	
    function grid() {
        $this->M_User->LoginRequired();
		
		$result['success'] = true;
		$result['rows'] = $this->M_Log->GetArray($_POST);
		$result['totalCount'] = $this->M_Log->GetCount($_POST);
        
        json_response($result);
    }
}

