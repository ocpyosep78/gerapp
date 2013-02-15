<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Jenis_Biaya extends CI_Model {
	function __construct() {
        parent::__construct();
		
		$this->Field = array('jenis_biaya_id', 'jenis_biaya', 'is_income');
    }
	
	function Update($Param) {
		$Result = array();
		
		if (empty($Param['jenis_biaya_id'])) {
			$InsertQuery  = GenerateInsertQuery($this->Field, $Param, JENIS_BIAYA);
			$InsertResult = mysql_query($InsertQuery) or die(mysql_error());
			
			$Result['jenis_biaya_id'] = mysql_insert_id();
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data berhasil tersimpan.';
		} else {
			$UpdateQuery  = GenerateUpdateQuery($this->Field, $Param, JENIS_BIAYA);
			$UpdateResult = mysql_query($UpdateQuery) or die(mysql_error());
			
			$Result['jenis_biaya_id'] = $Param['jenis_biaya_id'];
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data berhasil diperbaharui.';
		}
		
		return $Result;
	}
	
	function GetByID($Param) {
		$Array = array();
		
		if (isset($Param['jenis_biaya_id'])) {
			$SelectQuery  = "SELECT * FROM ".JENIS_BIAYA." WHERE jenis_biaya_id = '".$Param['jenis_biaya_id']."' LIMIT 1";
		}
		
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		if (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$Array = StripArray($Row);
		}
		
		return $Array;
	}
	
	function GetArray($Param = array()) {
		$Array = array();
		$StringSearch = (isset($Param['NameLike'])) ? "AND jenis_biaya LIKE '" . $Param['NameLike'] . "%'"  : '';
		$StringFilter = GetStringFilter($Param);
		
		$PageOffset = (isset($Param['start']) && !empty($Param['start'])) ? $Param['start'] : 0;
		$PageLimit = (isset($Param['limit']) && !empty($Param['limit'])) ? $Param['limit'] : 25;
		$StringSorting = (isset($Param['sort'])) ? GetStringSorting($Param['sort']) : 'jenis_biaya ASC';
		
		$SelectQuery = "
			SELECT JenisBiaya.*
			FROM ".JENIS_BIAYA." JenisBiaya
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
		
		$StringSearch = (isset($Param['NameLike'])) ? "AND jenis_biaya LIKE '" . $Param['NameLike'] . "%'"  : '';
		$StringFilter = GetStringFilter($Param);
		
		$SelectQuery = "
			SELECT COUNT(*) AS TotalRecord
			FROM ".JENIS_BIAYA." JenisBiaya
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
		$SelectQuery[] = "SELECT COUNT(*) RecordCount FROM ".PENDANAAN." WHERE jenis_biaya_id = '".$Param['jenis_biaya_id']."'";
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
		
		$DeleteQuery  = "DELETE FROM ".JENIS_BIAYA." WHERE jenis_biaya_id = '".$Param['jenis_biaya_id']."' LIMIT 1";
		$DeleteResult = mysql_query($DeleteQuery) or die(mysql_error());
		
		$Result['QueryStatus'] = '1';
		$Result['Message'] = 'Data berhasil dihapus.';
		
		return $Result;
	}
}