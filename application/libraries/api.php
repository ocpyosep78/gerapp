<?php
class api {
    function __construct() {
        $this->CI =& get_instance();
		$this->Curl = new CURL();
    }
	
	function get_token($Param) {
		date_default_timezone_set('UTC');
		$time = time();
		$hash = substr(md5($time . 'INDOCRM' . $Param->privatekey), 5, 8);
		$token = $time . '-' . $Param->client_id . '-' . $hash;
		
		return $token;
	}
	
	function request($url, $param) {
		// Generate Token
		$ArrayApiKey = $this->CI->M_Config->GetByID(array('config_name' => 'Api IndoCrm', 'gereja_id' => $param['gereja_id']));
		if (count($ArrayApiKey) == 0) {
			return array('ApiStatus' => 0);
		}
		$Api = json_decode($ArrayApiKey['config']);
		if (empty($Api->client_id) || empty($Api->privatekey)) {
			return array('ApiStatus' => 0);
		}
		$token = $this->get_token($Api);
		
		// Add Token
		$param['t'] = $token;
		$ResultJson = $this->Curl->post($url, $param);
		$Result = json_decode($ResultJson);
		$Result->ApiStatus = (isset($Result->success) && $Result->success) ? 1 : 0;
		unset($Result->success);
		
		// Debug Command
		// echo $url; print_r($param); exit;
		
		$Result = (array)$Result;
		return $Result;
	}
}
?>