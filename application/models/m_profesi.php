<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Profesi extends CI_Model {
	function __construct() {
        parent::__construct();
    }
	
	function Update($Profesi) {
		$Result = array();
		$Profesi = EscapeString($Profesi);
		
		if (empty($Profesi['ProfesiID'])) {
			$InsertQuery  = "
				INSERT INTO ".PROFESI." ( id, profesi )
				VALUES ( NULL, '".$Profesi['profesi']."' )";
			$InsertResult = mysql_query($InsertQuery) or die(mysql_error());
			
			$Result['ProfesiID'] = mysql_insert_id();
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data berhasil tersimpan.';
		} else {
			$UpdateQuery  = "
				UPDATE ".PROFESI."
				SET profesi = '".$Profesi['profesi']."'
				WHERE id = '".$Profesi['ProfesiID']."'
				LIMIT 1
			";
			$UpdateResult = mysql_query($UpdateQuery) or die(mysql_error());
			
			$Result['ProfesiID'] = $Profesi['ProfesiID'];
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data berhasil diperbaharui.';
		}
		
		return $Result;
	}
	
	function GetByID($Param) {
		$Array = array();
		
		if (isset($Param['ProfesiID'])) {
			$SelectQuery  = "SELECT * FROM ".PROFESI." WHERE id = '".$Param['ProfesiID']."' LIMIT 1";
		} else if (isset($Param['profesi'])) {
			$SelectQuery  = "SELECT * FROM ".PROFESI." WHERE profesi = '".$Param['profesi']."' LIMIT 1";
		}
		
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		if (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$Row['ProfesiID'] = $Row['id'];
			$Array = StripArray($Row);
		}
		
		return $Array;
	}
	
	function GetArray($Param = array()) {
		$Array = array();
		$StringSearch = (isset($Param['NameLike'])) ? "AND profesi LIKE '" . $Param['NameLike'] . "%'"  : '';
		$StringFilter = GetStringFilter($Param);
		
		$PageOffset = (isset($Param['start']) && !empty($Param['start'])) ? $Param['start'] : 0;
		$PageLimit = (isset($Param['limit']) && !empty($Param['limit'])) ? $Param['limit'] : 25;
		
		$SortingTemp = (isset($Param['sort']) && !empty($Param['sort'])) ? json_decode($Param['sort']) : 'profesi';
		$Sorting = (is_array($SortingTemp)) ? $SortingTemp[0]->property : $SortingTemp;
		$Ordering = (is_array($SortingTemp)) ? $SortingTemp[0]->direction : 'ASC';
		
		$SelectQuery = "
			SELECT Profesi.*
			FROM ".PROFESI." Profesi
			WHERE 1 $StringSearch $StringFilter
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
		
		$StringSearch = (isset($Param['NameLike'])) ? "AND profesi LIKE '" . $Param['NameLike'] . "%'"  : '';
		$StringFilter = GetStringFilter($Param);
		
		$SelectQuery = "
			SELECT COUNT(*) AS TotalRecord
			FROM ".PROFESI." Profesi
			WHERE 1 $StringSearch $StringFilter
		";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$TotalRecord = $Row['TotalRecord'];
		}
		
		return $TotalRecord;
	}
	
	function Delete($Param) {
        $DeleteQuery  = "DELETE FROM ".PROFESI." WHERE id = '".$Param['ProfesiID']."' LIMIT 1";
        $DeleteResult = mysql_query($DeleteQuery) or die(mysql_error());
        
        $Result['QueryStatus'] = '1';
        $Result['Message'] = 'Data berhasil dihapus.';
		
		return $Result;
	}
}