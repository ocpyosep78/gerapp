<?php

class report extends CI_Controller {
    public function __construct() {
        parent::__construct();
        
        $this->load->library('mpdf/mpdf');
    }
    
    function index($ReportName = '') {
        $this->M_User->LoginRequired();
		
		ini_set('memory_limit', '512M');
		$DirReportName = 'report/' . $ReportName;
		$ContentHtml = $this->load->view($DirReportName, array(), true);
		
		$Mpdf = new mPDF(); 
		$Mpdf->WriteHTML($ContentHtml);
		$Mpdf->Output();
		exit;
    }
}
