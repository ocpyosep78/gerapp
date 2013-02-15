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
class bill extends CI_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function index() {
        $this->M_User->LoginRequired();
        $this->load->view( 'finance/bill' );
    }
	
	function action() {
        $this->M_User->LoginRequired();
		$UserAdmin = $this->session->userdata('UserAdmin');
		$AccessGerejaID = $this->M_Permission->GetAccessGerejaID($UserAdmin);
		
		$Result = array();
		$Action = (isset($_POST['Action'])) ? $_POST['Action'] : '';
		
		if ($Action == 'UpdateBill') {
			$IuranAnak = $this->M_Config->GetByID(array('config_name' => 'Iuran Anak', 'gereja_id' => $_POST['gereja_id']));
			$IuranDewasa = $this->M_Config->GetByID(array('config_name' => 'Iuran Dewasa', 'gereja_id' => $_POST['gereja_id']));
			
			if ($_POST['sasaran_jemaat'] == 1) {
				if (empty($_POST['gereja_id'])) {
					$Result['QueryStatus'] = 0;
					$Result['Message'] = 'Harap memasukkan nama gereja.';
				} else {
					$Count = $this->M_Jemaat->GetCount();
					$ArrayJemaat = $this->M_Jemaat->GetArray(array('idgereja' => $_POST['gereja_id'], 'limit' => $Count));
					
					
					foreach ($ArrayJemaat as $Jemaat) {
						$ParamTagihan = array(
							'tagihan_id' => 0,
							'jemaat_id' => $Jemaat['id'],
							'tagihan_type_id' => $_POST['tagihan_type_id'],
							'tagihan_tanggal' => $_POST['tagihan_tanggal'],
							'tagihan_note' => $_POST['tagihan_note'],
							'InsertBy' => $UserAdmin['username'],
							'UpdateBy' => $UserAdmin['username'],
							'InsertTime' => $this->config->item('current_time'),
							'UpdateTime' => $this->config->item('current_time')
						);
						
						// Nilai Tagihan
						$Tagihan = $this->M_Tagihan->GetNilai(array(
							'tagihan_type_id' => $_POST['tagihan_type_id'], 'hubungankeluarga' => $Jemaat['hubungankeluarga'],
							'tagihan_nilai' => $_POST['tagihan_nilai'], 'IuranAnak' => $IuranAnak, 'IuranDewasa' => $IuranDewasa
						));
						$ParamTagihan['tagihan_nilai'] = $Tagihan['tagihan_nilai'];
						
						// Update Deposit
						$Deposit = $this->M_Jemaat->AutoUpdateDeposit(array('jemaat_id' => $Jemaat['id'], 'tagihan_nilai' => $ParamTagihan['tagihan_nilai']));
						$ParamTagihan['tagihan_bayar'] = $Deposit['tagihan_bayar'];
						
						$Result = $this->M_Tagihan->Update($ParamTagihan);
					}
				}
			}
			else if ($_POST['sasaran_jemaat'] == 2) {
				$Jemaat = $this->M_Jemaat->GetByID(array('JemaatID' => $_POST['jemaat_id']));
				
				// Nilai Tagihan
				$Tagihan = $this->M_Tagihan->GetNilai(array(
					'tagihan_type_id' => $_POST['tagihan_type_id'], 'hubungankeluarga' => $Jemaat['hubungankeluarga'],
					'tagihan_nilai' => $_POST['tagihan_nilai'], 'IuranAnak' => $IuranAnak, 'IuranDewasa' => $IuranDewasa
				));
				$_POST['tagihan_nilai'] = $Tagihan['tagihan_nilai'];
				
				// Update Deposit
				$Deposit = $this->M_Jemaat->AutoUpdateDeposit(array('jemaat_id' => $_POST['jemaat_id'], 'tagihan_nilai' => $_POST['tagihan_nilai']));
				$_POST['tagihan_bayar'] = $Deposit['tagihan_bayar'];
				
				$Result = $this->M_Tagihan->Update($_POST);
			}
		}
		else if ($Action == 'GetBillByID') {
			$Param = array(
				'jemaat_id' => $_POST['jemaat_id'],
				'tagihan_type_id' => $_POST['tagihan_type_id'],
				'diff_nilai' => 1,
				'sort' => '[{"property":"tagihan_id","direction":"ASC"}]'
			);
			$ArrayTagihan = $this->M_Tagihan->GetArray($Param);
			
			$Result = array('tagihan_nilai' => 0);
			$Result['tagihan_count'] = count($ArrayTagihan);
			foreach ($ArrayTagihan as $Key => $Array) {
				$Result['jemaat_id'] = $Array['jemaat_id'];
				$Result['jemaat_nama'] = $Array['jemaat_nama'];
				$Result['tagihan_type'] = $Array['tagihan_type'];
				$Result['tagihan_type_id'] = $Array['tagihan_type_id'];
				$Result['tagihan_nilai'] += $Array['tagihan_nilai'] - $Array['tagihan_bayar'];
			}
		}
		else if ($Action == 'UpdatePayment') {
			$Payment = $_POST['tagihan_bayar'];
			
			$Param = array(
				'jemaat_id' => $_POST['jemaat_id'],
				'tagihan_type_id' => $_POST['tagihan_type_id'],
				'diff_nilai' => 1,
				'sort' => '[{"property":"tagihan_id","direction":"ASC"}]'
			);
			$ArrayTagihan = $this->M_Tagihan->GetArray($Param);
			foreach ($ArrayTagihan as $Key => $Array) {
				$TagihanNilai = $Array['tagihan_nilai'] - $Array['tagihan_bayar'];
				$ParamUpdate['tagihan_id'] = $Array['tagihan_id'];
				
				if ($TagihanNilai > $Payment) {
					$ParamUpdate['tagihan_bayar'] = $Array['tagihan_bayar'] + $Payment;
					$Payment = 0;
				} else {
					$ParamUpdate['tagihan_bayar'] = $Array['tagihan_bayar'] + $TagihanNilai;
					$Payment = $Payment - $TagihanNilai;
				}
				
				$Result = $this->M_Tagihan->Update($ParamUpdate);
				
				if ($Payment == 0) {
					break;
				}
			}
			
			// Deposit
			if ($Payment > 0) {
				$this->M_Jemaat->UpdateCommon(array( 'id' => $_POST['jemaat_id'], 'deposit' => $Payment ));
			}
		}
		
		echo json_encode($Result);
	}
	
    function grid() {
        $this->M_User->LoginRequired();
		$User = $this->M_User->GetCurrentUser();
		
		if (!empty($User['gereja_id'])) {
			$_POST['gereja_id'] = $User['gereja_id'];
		}
		
		$result['success'] = true;
		$result['rows'] = $this->M_Tagihan->GetArrayGroup($_POST);
		$result['totalCount'] = $this->M_Tagihan->GetCountGroup($_POST);
        
        json_response($result);
    }
	
	function view($PageView = 'bill') {
		$this->load->view( 'finance/popup/' . $PageView );
	}
}