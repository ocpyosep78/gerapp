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

define('GEREJA_ID', 27);

class import extends CI_Controller {
    function __construct() {
        parent::__construct();
    }
    
    function je_convert() {
        $FileRecord = $this->config->item('base_path') . '/import/AllSim.csv';
		$ArrayRecord = file($FileRecord);
		
		$Content = '';
		foreach ($ArrayRecord as $Key => $RawRecord) {
			$ArrayRecord = explode(',', $RawRecord);
			
			for ($i = 0; $i < 10; $i++) {
				$ArrayRecord[$i] = (isset($ArrayRecord[$i])) ? $ArrayRecord[$i] : '';
			}
			
			// Validation Value
			$ArrayRecord[0] = trim($ArrayRecord[0]);
			
			if ($Key >= 10) {
				if ($ArrayRecord[0] == 'Kode Keluarga') {
					continue;
				} else if ($ArrayRecord[0] == 'Kartu Keluarga') {
					continue;
				} else if ($ArrayRecord[0] == 'Detail Anggota Keluarga') {
					continue;
				} else if (empty($ArrayRecord[0]) && empty($ArrayRecord[1]) && empty($ArrayRecord[2]) && empty($ArrayRecord[3]) && empty($ArrayRecord[4])) {
					continue;
				}
				
				if (!empty($ArrayRecord[0])) {
					$CheckString = preg_match('/^(Kel|Detail Anggota) .+/i', $ArrayRecord[0]);
					if ($CheckString) {
						continue;
					}
				}
				if (strlen($ArrayRecord[0]) >= 14) {
					$Value = substr($ArrayRecord[0], 0, 14);
					if ($Value == 'Kartu Keluarga') {
						continue;
					}
				}
				if (strlen($ArrayRecord[0]) >= 18) {
					$ValueSektorPelayanan = substr($ArrayRecord[0], 0, 16);
					if ($ValueSektorPelayanan == 'Sektor Pelayanan') {
						continue;
					}
				}
			}
			
			$Content .= $RawRecord;
		}
		
		$FileDest = $this->config->item('base_path') . '/import/AllSimResult.csv';
		Write($FileDest, $Content);
		echo 'Done';
    }
	
	function je_exec() {
		$Import = new ImportClass();
		$FileRecord = $this->config->item('base_path') . '/import/AllSimResult.csv';
		$ArrayFileRecord = file($FileRecord);
		$this->clean();
		
		$Limit = 2000;
		$ArrayResult = array( 'Keluarga' => 0, 'Jemaat' => 0 );
		foreach ($ArrayFileRecord as $Key => $RawRecord) {
			$Record = explode(',', $RawRecord);
			if (in_array($Key, array(0,1,2))) {
				continue;
			}
			
			if ($Import->IsKeluarga($Record)) {
				$Keluarga = $Import->InsertKeluaga($Record);
//				echo $Keluarga['nama'] . '<br />';
				$ArrayResult['Keluarga']++;
			} else if ($Import->IsJemaat($Record)) {
				$Jemaat = $Import->InsertJemaat($Record, $Keluarga);
				$ArrayResult['Jemaat']++;
			}
			
			$Limit--;
			if ($Limit < 0) {
				break;
			}
		}
		
		print_r($ArrayResult);
	}
	
	function clean() {
		$TruncateQuery = "TRUNCATE TABLE `keluarga`";
		$TruncateResult = mysql_query($TruncateQuery) or die(mysql_error());
		
		$TruncateQuery = "TRUNCATE TABLE `jemaat`";
		$TruncateResult = mysql_query($TruncateQuery) or die(mysql_error());
	}
	
	function ultah_convert() {
		$ImportClass = new ImportClass();
        $FileRecord = $this->config->item('base_path') . '/import/AllUltah.csv';
		$ArrayRecord = file($FileRecord);
		
		$Content = '';
		foreach ($ArrayRecord as $Key => $RawRecord) {
			$ArrayRecord = explode(',', $RawRecord);
			
			$ArrayRecord[0] = strtoupper($ArrayRecord[0]);
			$ArrayRecord[1] = $ImportClass->ConvertDate($ArrayRecord[1]);
			$ArrayRecord[2] = $ImportClass->GetSector($ArrayRecord[2]);
			$ResultRecord = implode(',', $ArrayRecord);
			
			$Content .= $ResultRecord . "\n";
		}
		
		$FileDest = $this->config->item('base_path') . '/import/AllUltahResult.csv';
		Write($FileDest, $Content);
		echo 'Done';
	}
	
	function ultah_exec() {
		$ImportClass = new ImportClass();
        $FileRecord = $this->config->item('base_path') . '/import/AllUltahResult.csv';
		$ArrayRecord = file($FileRecord);
		
		$Content = '';
		$TotalUpdate = 0;
		foreach ($ArrayRecord as $Key => $RawRecord) {
			$ArrayRecord = explode(',', $RawRecord);
			
			if (isset($ArrayRecord[2]))
				$ArrayRecord[2] = trim($ArrayRecord[2]);
			
			$Keluarga = $this->M_Keluarga->GetByID(array('nama' => $ArrayRecord[0]));
			if (count($Keluarga) > 0) {
				$ArrayRecord[3] = "'" . $Keluarga['nomor'] . "'";
				
				if ($Keluarga['sektor'] == $ArrayRecord[2]) {
					$ParamUpdate = array(
						'KeluargaID' => $Keluarga['id'],
						'ultah_perkawinan' => $ImportClass->GetStandartDate($ArrayRecord[1])
					);
					$this->M_Keluarga->Update($ParamUpdate);
					$TotalUpdate++;
				}
			}
			
			$Content .= implode(',', $ArrayRecord);
			$Content .= "\n";
		}
		
		$SelectQuery  = "SELECT * FROM ".KELUARGA." WHERE RIGHT(nama , 1) = '-'";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error($SelectQuery));
		while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$Jemaat = $this->M_Jemaat->GetByID(array('idkeluarga' => $Row['id']));
			
			$ParamUpdate = array( 'KeluargaID' => $Row['id'], 'nama' => $Jemaat['nama'] );
			$Result = $this->M_Keluarga->Update($ParamUpdate);
		}
		
		Write($FileRecord, $Content);
		echo 'Total Update : ' . $TotalUpdate;
	}
}

class ImportClass extends import {
    function __construct() {
        parent::__construct();
		
		$this->ArrayPendidikan = $this->M_Pendidikan->GetArray();
    }
	
	function IsKeluarga($Record) {
		$Record[1] = (isset($Record[1])) ? $Record[1] : '';
		
		$Result = false;
		if (empty($Record[0]) && !empty($Record[1])) {
			$Result = true;
		}
		
		return $Result;
	}

	function InsertKeluaga($Record) {
		$TanggalDaftar = (strlen($Record[2]) == 4) ? $Record[2] . '-01-01' : $Record[2];
		$NomorKeluarga = $this->M_Keluarga->GetMaxNo(array('idgereja' => GEREJA_ID));
		
		$Name = strtoupper($Record[1]);
		$Name = ($Name == '-') ? $NomorKeluarga . ' -' : $Name;
		
		$Alamat = $Record[5];
		$Alamat = (empty($Record[6])) ? $Alamat : $Alamat . ' ' . $Record[6];
		$Alamat = (empty($Record[7])) ? $Alamat : $Alamat . ' ' . $Record[7];
		$Alamat = (empty($Record[8])) ? $Alamat : $Alamat . ' ' . $Record[8];
		$Alamat = (empty($Record[9])) ? $Alamat : $Alamat . ' ' . $Record[9];
		$Alamat = (empty($Record[10])) ? $Alamat : $Alamat . ' ' . $Record[10];
		
		$Sektor = (empty($Record[4])) ? '7' : $Record[4];
		
		$ParamInsert = array(
			'KeluargaID' => 0,
			'nama' => $Name,
			'idgereja' => GEREJA_ID,
			'sektor' => $Sektor,
			'alamat' => $Alamat,
			'nomor' => $NomorKeluarga,
			'ultah_perkawinan' => '',
			'meninggal' => 0,
			'InsertBy' => 'admin',
			'UpdateBy' => 'admin',
			'InsertTime' => date("Y-m-d H:i:s"),
			'UpdateTime' => date("Y-m-d H:i:s")
		);
		$Result = $this->M_Keluarga->Update($ParamInsert);
		$Result['nomor'] = $NomorKeluarga;
		$Result['nama'] = $ParamInsert['nama'];
		$Result['alamat'] = $Alamat;
		$Result['tanggal_daftar'] = $TanggalDaftar;
		return $Result;
	}
	
	function IsJemaat($Record) {
		$Record[1] = (isset($Record[1])) ? $Record[1] : '';
		
		$Result = false;
		if (empty($Record[0]) && empty($Record[1]) && !empty($Record[2])) {
			$Result = true;
		}
		
		return $Result;
	}

	function InsertJemaat($Record, $Keluarga) {
		$NamaJemaat = $Record[2];
		$NamaJemaat = (empty($Record[3])) ? $NamaJemaat : $NamaJemaat . ' ' . $Record[3];
		
		$HubunganKeluarga = $this->GetHubunganKeluarga($Record[6]);
		$Nomor = $this->GetNomorJemaat($Keluarga['nomor'] . '-' . $HubunganKeluarga);
		
		// echo $this->GetPendidikan($Record[20]); exit;
		
		$ParamInsert = array(
			'id' => 0,
			'idgereja' => GEREJA_ID,
			'idkeluarga' => $Keluarga['KeluargaID'],
			'nama' => strtoupper($NamaJemaat),
			'nomor' => $Nomor,
			'tgllahir' => $this->GetStandartDate($Record[8]),
			'sektor' => $Record[33],
			'alamat' => $Keluarga['alamat'],
			'tempatlahir' => $Record[7],
			'golongandarah' => $this->GetGolonganDarah($Record[19]),
			'tanggaldaftar' => $this->GetStandartDate($Keluarga['tanggal_daftar']),
			'telpon' => $Record[28],
			'hp' => $Record[29],
			'catatan' => $Record[36],
			'firstname' => $Record[2],
			'lastname' => $Record[3],
			'email' => $Record[30],
			'sex' => $Record[5],
			'profesi' => $Record[34],
			'institusi' => $Record[24],
			'jabatan' => $Record[31],
			'statusbaptis' => $this->GetStatusBaptis($Record[5]),
			'tanggalbaptis' => $this->GetStandartDate($Record[12]),
			'statussidi' => $this->GetStatusSidi($Record[13]),
			'tanggalsidi' => $this->GetStandartDate($Record[15]),
			'status' => $this->GetStatusNikah($Record[16]),
			'statusnikah' => $this->GetStatusNikahBool($Record[16]),
			'tanggalnikah' => $this->GetStandartDate($Record[18]),
			'tempatpemberkatan' => $Record[11],
			'pendidikan' => $this->GetPendidikan($Record[20]),
			'gelar' => $Record[21],
			'jurusan' => $Record[22],
			'hubungankeluarga' => $HubunganKeluarga,
			'InsertBy' => 'admin',
			'UpdateBy' => 'admin',
			'InsertTime' => date("Y-m-d H:i:s"),
			'UpdateTime' => date("Y-m-d H:i:s")
		);
		$Result = $this->M_Jemaat->UpdateCommon($ParamInsert);
		
		return $Result;
	}
	
	function GetGolonganDarah($Value) {
		$Value = trim(strtoupper($Value));
		return $Value;
	}
	
	function GetStatusBaptis($Value) {
		$Value = trim(strtoupper($Value));
		$Result = ($Value == 'S') ? 1 : 0;
		return $Result;
	}
	
	function GetStatusSidi($Value) {
		$Value = trim(strtoupper($Value));
		$Result = ($Value == 'S') ? 1 : 0;
		return $Result;
	}
	
	function GetStatusNikah($Value) {
		$Value = intval($Value);
		$Value = (empty($Value)) ? 0 : $Value;
		
		$ArrayValue = array(
			'0' => 1, '1' => 2, 
			'2' => 3, '3' => 4
		);
		
		$Result = (isset($ArrayValue[$Value])) ? $ArrayValue[$Value] : '';
		
		return $Result;
	}
	
	function GetStatusNikahBool($Value) {
		$Value = intval($Value);
		$Result = (empty($Value)) ? 0 : 1;
		
		return $Result;
	}
	
	function GetPendidikan($Value) {
		$Result = trim(strtoupper($Value));
		return $Result;
	}
	
	function GetHubunganKeluarga($Value) {
		$ArrayValue = array(
			'KK' => '01', 'IS' => '02', 'AN' => '03n',
			'OT' => '04n', 'CU' => '05n', 'KA' => '06n',
			'MN' => '07n', 'FA' => '08n'
		);
		
		$Result = (isset($ArrayValue[$Value])) ? $ArrayValue[$Value] : '';
		return $Result;
	}
	
	function GetNomorJemaat($Value) {
		$CheckRaw = substr($Value, -1, 1);
		if ($CheckRaw == 'n') {
			$ArrayResult = $this->M_Jemaat->GetNextNomor(array('Nomor' => $Value));
			$Result = $ArrayResult['Nomor'];
		} else {
			$Result = $Value;
		}
		
		return $Result;
	}
	
	function GetStandartDate($Value) {
		if (empty($Value) || strlen($Value) < 10) {
			return '';
		}
		
		$Value = preg_replace('/[^0-9]+/i', '-', $Value);
		$ArrayValue = explode('-', $Value);
		
		$Result = '';
		if (strlen($ArrayValue[0]) == 4) {
			$Result = $ArrayValue[0] . '-' . $ArrayValue[1] . '-' . $ArrayValue[2];
		} else {
			if (count($ArrayValue) == 3) {
				$Result = $ArrayValue[2] . '-' . $ArrayValue[1] . '-' . $ArrayValue[0];
			}
		}
		
		return $Result;
	}
	
	function ConvertDate($Raw) {
		$Value = strtoupper($Raw);
		$Value = preg_replace('/[^0-9a-zA-Z]/i', ' ', $Value);
		
		$ArrayRaw = array(
			'JANUARI', 'FEBRUARI', 'MARET', 'APRIL',
			'MEI', 'JUNI', 'JULI', 'AGUSTUS',
			'SEPTEMBER', 'OKTOBER', 'NOVEMBER', 'NOV', 'NOPEMBER', 'DESEMBER'
		);
		$ArrayReplace = array(
			'01', '02', '03', '04',
			'05', '06', '07', '08',
			'09', '10', '11', '11', '11', '12'
		);
		
		$Value = str_replace($ArrayRaw, $ArrayReplace, $Value);
		$Value = preg_replace('/\ /i', '-', $Value);
		$Result = $this->GetStandartDate($Value);
		
		return $Value;
	}
	
	function GetSector($Value) {
		$Value = preg_replace('/[^0-9]/i', '', $Value);
		$Value = trim($Value);
		return $Value;
	}
}
?>