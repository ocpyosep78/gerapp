<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Keluarga extends CI_Model {
	function __construct() {
        parent::__construct();
		
		$this->Field = array(
			'id', 'nama', 'alamat', 'UpdateBy', 'InsertTime', 'UpdateTime', 'InsertBy', 'nomor', 'ultah_perkawinan', 'meninggal', 'sektor', 'idgereja',
			'sektor_id', 'no_kk', 'no_hp', 'customer_id'
		);
    }
	
	function Update($Param) {
		$Param['RequestApi'] = (isset($Param['RequestApi'])) ? $Param['RequestApi'] : 0;
		
		// Add Validation ID
		if (empty($Param['id']) && !empty($Param['KeluargaID'])) {
			$Param['id'] = $Param['KeluargaID'];
		} else if (empty($Param['KeluargaID']) && !empty($Param['id'])) {
			$Param['KeluargaID'] = $Param['id'];
		}
		
		$Result = array();
		if (empty($Param['KeluargaID'])) {
			$InsertQuery  = GenerateInsertQuery($this->Field, $Param, KELUARGA);
			$InsertResult = mysql_query($InsertQuery) or die(mysql_error());
			
			$Result['KeluargaID'] = mysql_insert_id();
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data berhasil tersimpan.';
			
			$this->M_Log->Write('Menambah Keluarga (' . $Result['KeluargaID'] . ')', $InsertQuery);
		} else {
			$UpdateQuery  = GenerateUpdateQuery($this->Field, $Param, KELUARGA);
			$UpdateResult = mysql_query($UpdateQuery) or die(mysql_error());
			
			$Result['KeluargaID'] = $Param['KeluargaID'];
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data berhasil diperbaharui.';
			
			$this->M_Log->Write('Mengubah Keluarga (' . $Result['KeluargaID'] . ')', $UpdateQuery);
		}
		
		// Synchronize Customer IndoCrm
		if ($Param['RequestApi'] == 1) {
			$ApiResult = $this->SyncCustomer(array('KeluargaID' => $Result['KeluargaID']));
			$Result['api_result'] = $ApiResult['ApiStatus'];
		}
		
		return $Result;
	}
	
	function GetByID($Param) {
		$Array = array();
		
		if (isset($Param['KeluargaID'])) {
			$SelectQuery  = "
				SELECT Keluarga.*, Gereja.nama gereja
				FROM ".KELUARGA." Keluarga
				LEFT JOIN ".GEREJA." Gereja ON Keluarga.idgereja = Gereja.id
				WHERE Keluarga.id = '".$Param['KeluargaID']."' LIMIT 1";
		} else if (isset($Param['nomor'])) {
			$SelectQuery  = "
				SELECT Keluarga.*
				FROM ".KELUARGA." Keluarga
				WHERE Keluarga.nomor = '".$Param['nomor']."' LIMIT 1";
		} else if (isset($Param['nama'])) {
			$SelectQuery  = "
				SELECT Keluarga.*
				FROM ".KELUARGA." Keluarga
				WHERE Keluarga.nama = '".$Param['nama']."' LIMIT 1";
		}
		
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		if (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$Row['KeluargaID'] = $Row['id'];
			$Array = StripArray($Row, array('ultah_perkawinan'));
		}
		
		return $Array;
	}
	
	function GetArray($Param = array()) {
		$Array = array();
		$StringSearch = (isset($Param['NameLike'])) ? "AND Keluarga.nama LIKE '" . $Param['NameLike'] . "%'"  : '';
		$StringGerejaID = (isset($Param['idgereja']) && !empty($Param['idgereja'])) ? "AND idgereja = '" . $Param['idgereja'] . "'"  : '';
		$StringKeluargaID = (isset($Param['idkeluarga']) && !empty($Param['idkeluarga'])) ? "AND Keluarga.id = '" . $Param['idkeluarga'] . "'"  : '';
		$StringFilter = GetStringFilter($Param, array('nama' => 'Keluarga.nama', 'alamat' => 'Keluarga.alamat', 'gereja' => 'Gereja.nama'));
		$StringCustom = (!empty($Param['StringCustom'])) ? $Param['StringCustom'] : '';
		$PageOffset = (isset($Param['start']) && !empty($Param['start'])) ? $Param['start'] : 0;
		$PageLimit = (isset($Param['limit']) && !empty($Param['limit'])) ? $Param['limit'] : 25;
		
		$SortingTemp = (isset($Param['sort']) && !empty($Param['sort'])) ? json_decode($Param['sort']) : 'nama';
		$Sorting = (is_array($SortingTemp)) ? $SortingTemp[0]->property : $SortingTemp;
		$Ordering = (is_array($SortingTemp)) ? $SortingTemp[0]->direction : 'ASC';
		
		$SelectQuery = "
			SELECT
				Keluarga.*, CONCAT(MONTH(ultah_perkawinan), ' ', DAY(ultah_perkawinan)) ultah_perkawinan_monthday,
				Gereja.nama gereja, Sektor.sektor
			FROM ".KELUARGA." Keluarga
			LEFT JOIN ".GEREJA." Gereja ON Keluarga.idgereja = Gereja.id
			LEFT JOIN ".SEKTOR." Sektor ON Sektor.sektor_id = Keluarga.sektor_id
			WHERE 1 $StringSearch $StringGerejaID $StringKeluargaID $StringFilter $StringCustom
			ORDER BY $Sorting $Ordering
			LIMIT $PageOffset, $PageLimit
		";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$Row = StripArray($Row, array('ultah_perkawinan'));
			$Array[] = $Row;
		}
		
		return $Array;
	}
	
	function GetCount($Param = array()) {
		$TotalRecord = 0;
		
		$StringSearch = (isset($Param['NameLike'])) ? "AND Keluarga.nama LIKE '" . $Param['NameLike'] . "%'"  : '';
		$StringGerejaID = (isset($Param['idgereja']) && !empty($Param['idgereja'])) ? "AND idgereja = '" . $Param['idgereja'] . "'"  : '';
		$StringKeluargaID = (isset($Param['idkeluarga']) && !empty($Param['idkeluarga'])) ? "AND Keluarga.id = '" . $Param['idkeluarga'] . "'"  : '';
		$StringFilter = GetStringFilter($Param, array('nama' => 'Keluarga.nama', 'alamat' => 'Keluarga.alamat', 'gereja' => 'Gereja.nama'));
		
		$SelectQuery = "
			SELECT COUNT(*) AS TotalRecord
			FROM ".KELUARGA." Keluarga
			LEFT JOIN ".GEREJA." Gereja ON Keluarga.idgereja = Gereja.id
			WHERE 1 $StringSearch $StringGerejaID $StringKeluargaID $StringFilter
		";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$TotalRecord = $Row['TotalRecord'];
		}
		
		return $TotalRecord;
	}
	
	function GetMaxNo($Param = array()) {
		$NextNo = '0001';
		$StringGerejaID = (isset($Param['idgereja']) && !empty($Param['idgereja'])) ? "AND idgereja = '" . $Param['idgereja'] . "'"  : '';
		
		$SelectQuery = "
			SELECT nomor FROM ".KELUARGA." Keluarga
			WHERE 1 $StringGerejaID
			ORDER BY nomor DESC LIMIT 1
		";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		if (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$RawNo = $Row['nomor'];
			$RawNo = preg_replace('/[^0-9]/i', '', $RawNo);
			$RawNo = intval($RawNo) + 1;
			$NextNo = str_pad($RawNo, 4, "0", STR_PAD_LEFT);
		}
		
		return $NextNo;
	}
	
	function Delete($Param) {
		$RecordCount = 0;
		$SelectQuery = array();
		$SelectQuery[] = "SELECT COUNT(*) RecordCount FROM ".JEMAAT." WHERE idkeluarga = '".$Param['KeluargaID']."'";
        foreach ($SelectQuery as $Query) {
            $SelectResult = mysql_query($Query) or die(mysql_error());
            if (false !== $Row = mysql_fetch_assoc($SelectResult)) {
                $RecordCount += $Row['RecordCount'];
            }
        }
		if ($RecordCount > 0) {
            $Result['QueryStatus'] = '0';
            $Result['Message'] = 'Data tidak bisa dihapus karena data telah terpakai.';
			return $Result;
		}
		
		$DeleteQuery  = "DELETE FROM ".KELUARGA." WHERE id = '".$Param['KeluargaID']."' LIMIT 1";
		$DeleteResult = mysql_query($DeleteQuery) or die(mysql_error());
		
		$Result['QueryStatus'] = '1';
		$Result['Message'] = 'Data berhasil dihapus.';
		
		$this->M_Log->Write('Menghapus Keluarga (' . $Param['KeluargaID'] . ')', $DeleteQuery);
		
		return $Result;
	}
	
	// Synchronize Customer IndoCrm
	function SyncCustomer($Param) {
		$Keluarga = $this->GetByID(array( 'KeluargaID' => $Param['KeluargaID'] ));
		
		// Validation Name
		$ArrayName = explode(' ', $Keluarga['nama'], 2);
		$Keluarga['firstname'] = $ArrayName[0];
		$Keluarga['lastname'] = (empty($ArrayName[1])) ? '' : $ArrayName[1];
		
		$ApiParam = array(
			'action' => 'Update',
			'gereja_id' => $Keluarga['idgereja'],
			'customer_id' => $Keluarga['customer_id'],
			'first_name' => $Keluarga['firstname'],
			'last_name' => $Keluarga['lastname'],
			'address' => $Keluarga['alamat'],
			'mobile' => $Keluarga['no_hp'],
			'customer_category' => 'Gereja Apps Keluarga'
		);
		$Result = $this->api->request($this->config->item('indocrm_api') . 'customer', $ApiParam);
		if (!empty($Result['ApiStatus']) && $Result['ApiStatus'] == 1 && empty($Keluarga['customer_id'])) {
			$this->Update(array( 'id' => $Param['KeluargaID'], 'customer_id' => $Result['customer_id'], 'RequestApi' => 0 ));
		}
		
		return $Result;
	}
}