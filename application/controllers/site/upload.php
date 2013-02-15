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
class upload extends CI_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
        $this->M_User->LoginRequired();
		$Action = $this->input->post('Action');
		
		if ($Action == 'UploadImage') {
			$Result['success'] = true;
			$Result['UploadPhoto'] = '';
			$Result['UploadMessage'] = 'Upload Fail.';
			$Result['UploadStatus'] = 1;
			
			// Update Foto
			$photo_upload = Upload('photo_upload', $this->config->item('base_path') . '/images/logo');
			if (!empty($photo_upload['FileDirName'])) {
				$Result['UploadStatus'] = 1;
				$Result['UploadMessage'] = 'Upload successful';
				$Result['UploadPhoto'] = $photo_upload['FileDirName'];
				
				$PathFileName = $this->config->item('base_path') . '/images/logo/' . $Result['UploadPhoto'];
				$ImageProperty = @getimagesize($PathFileName);
				if ($ImageProperty) {
					$Result['width'] = $ImageProperty[0];
					$Result['height'] = $ImageProperty[1];
				}
			}
		}
		
		echo json_encode($Result);
    }
}

