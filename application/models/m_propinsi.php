<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Propinsi extends CI_Model {
	function __construct() {
        parent::__construct();
    }
	
	function Update($Propinsi) {
		$Result = array();
		$Propinsi = EscapeString($Propinsi);
		
		if (empty($Propinsi['PropinsiID'])) {
			$InsertQuery  = "
				INSERT INTO ".PROPINSI." ( id, propinsi )
				VALUES ( NULL, '".$Propinsi['propinsi']."' )";
			$InsertResult = mysql_query($InsertQuery) or die(mysql_error());
			
			$Result['PropinsiID'] = mysql_insert_id();
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data berhasil tersimpan.';
		} else {
			$UpdateQuery  = "
				UPDATE ".PROPINSI."
				SET propinsi = '".$Propinsi['propinsi']."'
				WHERE id = '".$Propinsi['PropinsiID']."'
				LIMIT 1
			";
			$UpdateResult = mysql_query($UpdateQuery) or die(mysql_error());
			
			$Result['PropinsiID'] = $Propinsi['PropinsiID'];
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data berhasil diperbaharui.';
		}
		
		return $Result;
	}
	
	function GetByID($Param) {
		$Array = array();
		
		if (isset($Param['PropinsiID'])) {
			$SelectQuery  = "SELECT * FROM ".PROPINSI." WHERE id = '".$Param['PropinsiID']."' LIMIT 1";
		}
		
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		if (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$Array = StripArray($Row);
		}
		
		return $Array;
	}
	
	function GetArray($Param = array()) {
		$Array = array();
		$StringSearch = (isset($Param['NameLike'])) ? "AND propinsi LIKE '" . $Param['NameLike'] . "%'"  : '';
		$StringNegara = (isset($Param['idnegara']) && !empty($Param['idnegara'])) ? "AND idnegara = '" . $Param['idnegara'] . "'"  : '';
		$StringFilter = GetStringFilter($Param);
		
		$PageOffset = (isset($Param['start']) && !empty($Param['start'])) ? $Param['start'] : 0;
		$PageLimit = (isset($Param['limit']) && !empty($Param['limit'])) ? $Param['limit'] : 25;
		
		$SortingTemp = (isset($Param['sort']) && !empty($Param['sort'])) ? json_decode($Param['sort']) : 'propinsi';
		$Sorting = (is_array($SortingTemp)) ? $SortingTemp[0]->property : $SortingTemp;
		$Ordering = (is_array($SortingTemp)) ? $SortingTemp[0]->direction : 'ASC';
		
		$SelectQuery = "
			SELECT Propinsi.*
			FROM ".PROPINSI." Propinsi
			WHERE 1 $StringSearch $StringNegara $StringFilter
			ORDER BY $Sorting $Ordering
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
		
		$StringSearch = (isset($Param['NameLike'])) ? "AND propinsi LIKE '" . $Param['NameLike'] . "%'"  : '';
		$StringNegara = (isset($Param['idnegara'])) ? "AND idnegara = '" . $Param['idnegara'] . "'"  : '';
		$StringFilter = GetStringFilter($Param);
		
		$SelectQuery = "
			SELECT COUNT(*) AS TotalRecord
			FROM ".PROPINSI." Propinsi
			WHERE 1 $StringSearch $StringNegara $StringFilter
		";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$TotalRecord = $Row['TotalRecord'];
		}
		
		return $TotalRecord;
	}
	
	function Delete($Param) {
        $DeleteQuery  = "DELETE FROM ".PROPINSI." WHERE id = '".$Param['PropinsiID']."' LIMIT 1";
        $DeleteResult = mysql_query($DeleteQuery) or die(mysql_error());
        
        $Result['QueryStatus'] = '1';
        $Result['Message'] = 'Data berhasil dihapus.';
		
		return $Result;
	}
}