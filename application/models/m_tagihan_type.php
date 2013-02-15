<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Tagihan_Type extends CI_Model {
	function __construct() {
        parent::__construct();
		
		$this->Field = array('tagihan_type_id', 'tagihan_type');
    }
	
	function Update($Param) {
		$Result = array();
		
		if (empty($Param['tagihan_type_id'])) {
			$InsertQuery  = GenerateInsertQuery($this->Field, $Param, TAGIHAN_TYPE);
			$InsertResult = mysql_query($InsertQuery) or die(mysql_error());
			
			$Result['tagihan_type_id'] = mysql_insert_id();
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data berhasil tersimpan.';
		} else {
			$UpdateQuery  = GenerateUpdateQuery($this->Field, $Param, TAGIHAN_TYPE);
			$UpdateResult = mysql_query($UpdateQuery) or die(mysql_error());
			
			$Result['tagihan_type_id'] = $Param['tagihan_type_id'];
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data berhasil diperbaharui.';
		}
		
		return $Result;
	}
	
	function GetByID($Param) {
		$Array = array();
		
		if (isset($Param['tagihan_type_id'])) {
			$SelectQuery  = "
				SELECT TagihanType.*
				FROM ".TAGIHAN_TYPE." TagihanType
				WHERE TagihanType.id = '".$Param['tagihan_type_id']."' LIMIT 1";
		}
		
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		if (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$Array = StripArray($Row);
		}
		
		return $Array;
	}
	
	function GetArray($Param = array()) {
		$Array = array();
		
		$PageOffset = (isset($Param['start']) && !empty($Param['start'])) ? $Param['start'] : 0;
		$PageLimit = (isset($Param['limit']) && !empty($Param['limit'])) ? $Param['limit'] : 25;
		$StringSorting = (isset($Param['sort'])) ? GetStringSorting($Param['sort']) : 'tagihan_type ASC';
		
		$SelectQuery = "
			SELECT *
			FROM ".TAGIHAN_TYPE." TagihanType
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
		
		$SelectQuery = "
			SELECT COUNT(*) AS TotalRecord
			FROM ".TAGIHAN_TYPE." TagihanType
			WHERE 1 $StringSearch $StringGerejaID $StringFilter
		";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$TotalRecord = $Row['TotalRecord'];
		}
		
		return $TotalRecord;
	}
	
	function Delete($Param) {
		$RecordCount = 0;
		$SelectQuery  = "
			SELECT COUNT(*) RecordCount
			FROM ".TAGIHAN."
			WHERE tagihan_type_id = '".$Param['tagihan_type_id']."'
		";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		if (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$RecordCount = $Row['RecordCount'];
		}
		
		if ($RecordCount == 0) {
			$DeleteQuery  = "DELETE FROM ".TAGIHAN_TYPE." WHERE tagihan_type_id = '".$Param['tagihan_type_id']."' LIMIT 1";
			$DeleteResult = mysql_query($DeleteQuery) or die(mysql_error());
			
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data berhasil dihapus.';
		} else {
			$Result['QueryStatus'] = '0';
			$Result['Message'] = 'Data tidak bisa dihapus karena data telah terpakai.';
		}
		
		return $Result;
	}
}