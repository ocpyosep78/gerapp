<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Metode_Kirim extends CI_Model {
	function __construct() {
        parent::__construct();
		
		$this->Field = array('metode_kirim_id', 'metode_kirim');
    }
	
	function Update($Param) {
		$Result = array();
		
		if (empty($Param['metode_kirim_id'])) {
			$InsertQuery  = GenerateInsertQuery($this->Field, $Param, METODE_KIRIM);
			$InsertResult = mysql_query($InsertQuery) or die(mysql_error());
			
			$Result['metode_kirim_id'] = mysql_insert_id();
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data berhasil tersimpan.';
		} else {
			$UpdateQuery  = GenerateUpdateQuery($this->Field, $Param, METODE_KIRIM);
			$UpdateResult = mysql_query($UpdateQuery) or die(mysql_error());
			
			$Result['metode_kirim_id'] = $Param['metode_kirim_id'];
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data berhasil diperbaharui.';
		}
		
		return $Result;
	}
	
	function GetByID($Param) {
		$Array = array();
		
		if (isset($Param['metode_kirim_id'])) {
			$SelectQuery  = "SELECT * FROM ".METODE_KIRIM." WHERE metode_kirim_id = '".$Param['metode_kirim_id']."' LIMIT 1";
		}
		
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		if (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$Array = StripArray($Row);
		}
		
		return $Array;
	}
	
	function GetArray($Param = array()) {
		$Array = array();
		$StringSearch = (isset($Param['NameLike'])) ? "AND metode_kirim LIKE '" . $Param['NameLike'] . "%'"  : '';
		$StringFilter = GetStringFilter($Param);
		
		$PageOffset = (isset($Param['start']) && !empty($Param['start'])) ? $Param['start'] : 0;
		$PageLimit = (isset($Param['limit']) && !empty($Param['limit'])) ? $Param['limit'] : 25;
		$StringSorting = (isset($Param['sort'])) ? GetStringSorting($Param['sort']) : 'metode_kirim ASC';
		
		$SelectQuery = "
			SELECT MetodeKirim.*
			FROM ".METODE_KIRIM." MetodeKirim
			WHERE 1 $StringSearch $StringFilter
			ORDER BY $StringSorting
			LIMIT $PageOffset, $PageLimit
		";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$Row = StripArray($Row);
			$Array[] = $Row;
		}
		
		return $Array;
	}
	
	function GetCount($Param = array()) {
		$TotalRecord = 0;
		
		$StringSearch = (isset($Param['NameLike'])) ? "AND metode_kirim LIKE '" . $Param['NameLike'] . "%'"  : '';
		$StringFilter = GetStringFilter($Param);
		
		$SelectQuery = "
			SELECT COUNT(*) AS TotalRecord
			FROM ".METODE_KIRIM." MetodeKirim
			WHERE 1 $StringSearch $StringFilter
		";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$TotalRecord = $Row['TotalRecord'];
		}
		
		return $TotalRecord;
	}
	
	function Delete($Param) {
		$RecordCount = 0;
		$SelectQuery = array();
		$SelectQuery[] = "SELECT COUNT(*) RecordCount FROM ".PENDANAAN." WHERE metode_kirim_id = '".$Param['metode_kirim_id']."'";
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
		
		$DeleteQuery  = "DELETE FROM ".METODE_KIRIM." WHERE metode_kirim_id = '".$Param['metode_kirim_id']."' LIMIT 1";
		$DeleteResult = mysql_query($DeleteQuery) or die(mysql_error());
		
		$Result['QueryStatus'] = '1';
		$Result['Message'] = 'Data berhasil dihapus.';
		
		return $Result;
	}
}