<?php

class jemaat_excel extends CI_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
        $this->M_User->LoginRequired();
        $this->load->view( 'jemaat/jemaat_excel' );
    }
}

