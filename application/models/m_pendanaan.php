<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Pendanaan extends CI_Model {
	function __construct() {
        parent::__construct();
		
		$this->Field = array('pendanaan_id', 'sektor_id', 'jenis_biaya_id', 'metode_kirim_id', 'pendanaan', 'pendanaan_jumlah', 'pendanaan_tanggal');
    }
	
	function Update($Param) {
		$Result = array();
		
		if (empty($Param['pendanaan_id'])) {
			$InsertQuery  = GenerateInsertQuery($this->Field, $Param, PENDANAAN);
			$InsertResult = mysql_query($InsertQuery) or die(mysql_error());
			
			$Result['pendanaan_id'] = mysql_insert_id();
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data berhasil tersimpan.';
			
			$this->M_Log->Write('Menambah Pendanaan (' . $Result['pendanaan_id'] . ')', $InsertQuery);
		} else {
			$UpdateQuery  = GenerateUpdateQuery($this->Field, $Param, PENDANAAN);
			$UpdateResult = mysql_query($UpdateQuery) or die(mysql_error());
			
			$Result['pendanaan_id'] = $Param['pendanaan_id'];
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data berhasil diperbaharui.';
			
			$this->M_Log->Write('Mengubah Pendanaan (' . $Result['pendanaan_id'] . ')', $UpdateQuery);
		}
		
		return $Result;
	}
	
	function GetByID($Param) {
		$Array = array();
		
		if (isset($Param['pendanaan_id'])) {
			$SelectQuery  = "SELECT * FROM ".PENDANAAN." WHERE pendanaan_id = '".$Param['pendanaan_id']."' LIMIT 1";
		}
		
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		if (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$Array = StripArray($Row);
		}
		
		return $Array;
	}
	
	function GetArray($Param = array()) {
		$Array = array();
		$StringSearch = (isset($Param['NameLike'])) ? "AND pendanaan LIKE '" . $Param['NameLike'] . "%'"  : '';
		$StringFilter = GetStringFilter($Param);
		
		$PageOffset = (isset($Param['start']) && !empty($Param['start'])) ? $Param['start'] : 0;
		$PageLimit = (isset($Param['limit']) && !empty($Param['limit'])) ? $Param['limit'] : 25;
		$StringSorting = (isset($Param['sort'])) ? GetStringSorting($Param['sort']) : 'pendanaan ASC';
		
		$SelectQuery = "
			SELECT Pendanaan.*, Sektor.sektor, JenisBiaya.jenis_biaya, JenisBiaya.is_income, MetodeKirim.metode_kirim
			FROM ".PENDANAAN." Pendanaan
			LEFT JOIN ".SEKTOR." Sektor ON Sektor.sektor_id = Pendanaan.sektor_id
			LEFT JOIN ".JENIS_BIAYA." JenisBiaya ON JenisBiaya.jenis_biaya_id = Pendanaan.jenis_biaya_id
			LEFT JOIN ".METODE_KIRIM." MetodeKirim ON MetodeKirim.metode_kirim_id = Pendanaan.metode_kirim_id
			WHERE 1 $StringSearch $StringFilter
			ORDER BY $StringSorting
			LIMIT $PageOffset, $PageLimit
		";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$Row['pendanaan_tanggal'] = ($Row['pendanaan_tanggal'] == '0000-00-00') ? null : $Row['pendanaan_tanggal'];
			
			$Row = StripArray($Row);
			$Array[] = $Row;
		}
		
		return $Array;
	}
	
	function GetCount($Param = array()) {
		$TotalRecord = 0;
		
		$StringSearch = (isset($Param['NameLike'])) ? "AND pendanaan LIKE '" . $Param['NameLike'] . "%'"  : '';
		$StringFilter = GetStringFilter($Param);
		
		$SelectQuery = "
			SELECT COUNT(*) AS TotalRecord
			FROM ".PENDANAAN." Pendanaan
			LEFT JOIN ".SEKTOR." Sektor ON Sektor.sektor_id = Pendanaan.sektor_id
			LEFT JOIN ".JENIS_BIAYA." JenisBiaya ON JenisBiaya.jenis_biaya_id = Pendanaan.jenis_biaya_id
			LEFT JOIN ".METODE_KIRIM." MetodeKirim ON MetodeKirim.metode_kirim_id = Pendanaan.metode_kirim_id
			WHERE 1 $StringSearch $StringFilter
		";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$TotalRecord = $Row['TotalRecord'];
		}
		
		return $TotalRecord;
	}
	
	function Delete($Param) {
        $DeleteQuery  = "DELETE FROM ".PENDANAAN." WHERE pendanaan_id = '".$Param['pendanaan_id']."' LIMIT 1";
        $DeleteResult = mysql_query($DeleteQuery) or die(mysql_error());
		
        $Result['QueryStatus'] = '1';
        $Result['Message'] = 'Data berhasil dihapus.';
		
		$this->M_Log->Write('Menghapus Pendanaan (' . $Param['pendanaan_id'] . ')', $DeleteQuery);
		
		return $Result;
	}
}