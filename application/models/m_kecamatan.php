<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Kecamatan extends CI_Model {
	function __construct() {
        parent::__construct();
    }
	
	function Update($Kecamatan) {
		$Result = array();
		$Kecamatan = EscapeString($Kecamatan);
		
		if (empty($Kecamatan['KecamatanID'])) {
			$InsertQuery  = "
				INSERT INTO ".KECAMATAN." ( id, kecamatan )
				VALUES ( NULL, '".$Kecamatan['kecamatan']."' )";
			$InsertResult = mysql_query($InsertQuery) or die(mysql_error());
			
			$Result['KecamatanID'] = mysql_insert_id();
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data berhasil tersimpan.';
		} else {
			$UpdateQuery  = "
				UPDATE ".KECAMATAN."
				SET kecamatan = '".$Kecamatan['kecamatan']."'
				WHERE id = '".$Kecamatan['KecamatanID']."'
				LIMIT 1
			";
			$UpdateResult = mysql_query($UpdateQuery) or die(mysql_error());
			
			$Result['KecamatanID'] = $Kecamatan['KecamatanID'];
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data berhasil diperbaharui.';
		}
		
		return $Result;
	}
	
	function GetByID($Param) {
		$Array = array();
		
		if (isset($Param['KecamatanID'])) {
			$SelectQuery  = "SELECT * FROM ".KECAMATAN." WHERE id = '".$Param['KecamatanID']."' LIMIT 1";
		}
		
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		if (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$Array = StripArray($Row);
		}
		
		return $Array;
	}
	
	function GetArray($Param = array()) {
		$Array = array();
		$StringSearch = (isset($Param['NameLike'])) ? "AND kecamatan LIKE '" . $Param['NameLike'] . "%'"  : '';
		$StringFilter = GetStringFilter($Param);
		
		$PageOffset = (isset($Param['start']) && !empty($Param['start'])) ? $Param['start'] : 0;
		$PageLimit = (isset($Param['limit']) && !empty($Param['limit'])) ? $Param['limit'] : 25;
		
		$SortingTemp = (isset($Param['sort']) && !empty($Param['sort'])) ? json_decode($Param['sort']) : 'kecamatan';
		$Sorting = (is_array($SortingTemp)) ? $SortingTemp[0]->property : $SortingTemp;
		$Ordering = (is_array($SortingTemp)) ? $SortingTemp[0]->direction : 'ASC';
		
		$SelectQuery = "
			SELECT Kecamatan.*
			FROM ".KECAMATAN." Kecamatan
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
		
		$StringSearch = (isset($Param['NameLike'])) ? "AND kecamatan LIKE '" . $Param['NameLike'] . "%'"  : '';
		$StringFilter = GetStringFilter($Param);
		
		$SelectQuery = "
			SELECT COUNT(*) AS TotalRecord
			FROM ".KECAMATAN." Kecamatan
			WHERE 1 $StringSearch $StringFilter
		";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$TotalRecord = $Row['TotalRecord'];
		}
		
		return $TotalRecord;
	}
	
	function Delete($Param) {
        $DeleteQuery  = "DELETE FROM ".KECAMATAN." WHERE id = '".$Param['KecamatanID']."' LIMIT 1";
        $DeleteResult = mysql_query($DeleteQuery) or die(mysql_error());
        
        $Result['QueryStatus'] = '1';
        $Result['Message'] = 'Data berhasil dihapus.';
		
		return $Result;
	}
}