<?php
class gereja extends CI_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
        exit;
    }
	
	function action() {
        $this->M_User->LoginRequired();
		
		$Result = array();
		$Action = (isset($_POST['Action'])) ? $_POST['Action'] : '';
		
		if ($Action == 'UpdateGerejaApi') {
			$ParamUpdate = array(
				'config_id' => $_POST['config_id'],
				'config' => json_encode(array( 'client_id' => $_POST['client_id'], 'privatekey' => $_POST['privatekey'] )),
			);
			$Result = $this->M_Config->Update($ParamUpdate);
		} else if ($Action == 'GetGerejaApiByID') {
			$GetParam['config_name'] = 'Api IndoCrm';
			$GetParam['gereja_id'] = $_POST['gereja_id'];
			$Result = $this->M_Config->GetByID($GetParam);
			
			if (count($Result) == 0) {
				$InsertParam = array(
					'config_id' => 0,
					'gereja_id' => $_POST['gereja_id'],
					'config_name' => 'Api IndoCrm',
					'config' => '{"client_id":"","privatekey":""}',
					'hidden' => 1
				);
				$this->M_Config->Update($InsertParam);
				$Result = $this->M_Config->GetByID($GetParam);
			}
			
			$Api = (array)json_decode($Result['config']);
			$Result = array_merge($Result, $Api);
		}
		
		echo json_encode($Result);
	}
	
	function view($PageView) {
        $this->M_User->LoginRequired();
		$this->load->view( 'gereja/popup/' . $PageView);
	}
}