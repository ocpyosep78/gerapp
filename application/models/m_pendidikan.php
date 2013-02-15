<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Pendidikan extends CI_Model {
	function __construct() {
        parent::__construct();
    }
	
	function Update($Pendidikan) {
		$Result = array();
		$Pendidikan = EscapeString($Pendidikan);
		
		if (empty($Pendidikan['PendidikanID'])) {
			$InsertQuery  = "
				INSERT INTO ".PENDIDIKAN." ( id, pendidikan )
				VALUES ( NULL, '".$Pendidikan['pendidikan']."' )";
			$InsertResult = mysql_query($InsertQuery) or die(mysql_error());
			
			$Result['PendidikanID'] = mysql_insert_id();
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data berhasil tersimpan.';
		} else {
			$UpdateQuery  = "
				UPDATE ".PENDIDIKAN."
				SET pendidikan = '".$Pendidikan['pendidikan']."'
				WHERE id = '".$Pendidikan['PendidikanID']."'
				LIMIT 1
			";
			$UpdateResult = mysql_query($UpdateQuery) or die(mysql_error());
			
			$Result['PendidikanID'] = $Pendidikan['PendidikanID'];
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data berhasil diperbaharui.';
		}
		
		return $Result;
	}
	
	function GetByID($Param) {
		$Array = array();
		
		if (isset($Param['PendidikanID'])) {
			$SelectQuery  = "SELECT * FROM ".PENDIDIKAN." WHERE id = '".$Param['PendidikanID']."' LIMIT 1";
		} else if (isset($Param['pendidikan'])) {
			$SelectQuery  = "SELECT * FROM ".PENDIDIKAN." WHERE pendidikan = '".$Param['pendidikan']."' LIMIT 1";
		}
		
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		if (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$Row['PendidikanID'] = $Row['id'];
			$Array = StripArray($Row);
		}
		
		return $Array;
	}
	
	function GetArray($Param = array()) {
		$Array = array();
		$StringSearch = (isset($Param['NameLike'])) ? "AND pendidikan LIKE '" . $Param['NameLike'] . "%'"  : '';
		$StringFilter = GetStringFilter($Param);
		
		$PageOffset = (isset($Param['start']) && !empty($Param['start'])) ? $Param['start'] : 0;
		$PageLimit = (isset($Param['limit']) && !empty($Param['limit'])) ? $Param['limit'] : 25;
		
		$SortingTemp = (isset($Param['sort']) && !empty($Param['sort'])) ? json_decode($Param['sort']) : 'pendidikan';
		$Sorting = (is_array($SortingTemp)) ? $SortingTemp[0]->property : $SortingTemp;
		$Ordering = (is_array($SortingTemp)) ? $SortingTemp[0]->direction : 'ASC';
		
		$SelectQuery = "
			SELECT *
			FROM ".PENDIDIKAN."
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
		
		$StringSearch = (isset($Param['NameLike'])) ? "AND pendidikan LIKE '" . $Param['NameLike'] . "%'"  : '';
		$StringFilter = GetStringFilter($Param);
		
		$SelectQuery = "
			SELECT COUNT(*) AS TotalRecord
			FROM ".PENDIDIKAN."
			WHERE 1 $StringSearch $StringFilter
		";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$TotalRecord = $Row['TotalRecord'];
		}
		
		return $TotalRecord;
	}
	
	function Delete($Param) {
        $DeleteQuery  = "DELETE FROM ".PENDIDIKAN." WHERE id = '".$Param['PendidikanID']."' LIMIT 1";
        $DeleteResult = mysql_query($DeleteQuery) or die(mysql_error());
        
        $Result['QueryStatus'] = '1';
        $Result['Message'] = 'Data berhasil dihapus.';
		
		return $Result;
	}
}