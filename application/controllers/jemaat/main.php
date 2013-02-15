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
class main extends CI_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
    }
	
	function import() {
        $this->M_User->LoginRequired();
        $this->load->view( 'jemaat/popup/import_csv' );
	}
}

