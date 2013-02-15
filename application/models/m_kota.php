<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Kota extends CI_Model {
	function __construct() {
        parent::__construct();
    }
	
	function Update($Kota) {
		$Result = array();
		$Kota = EscapeString($Kota);
		
		if (empty($Kota['KotaID'])) {
			$InsertQuery  = "
				INSERT INTO ".KOTA." ( id, kota )
				VALUES ( NULL, '".$Kota['kota']."' )";
			$InsertResult = mysql_query($InsertQuery) or die(mysql_error());
			
			$Result['KotaID'] = mysql_insert_id();
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data berhasil tersimpan.';
		} else {
			$UpdateQuery  = "
				UPDATE ".KOTA."
				SET kota = '".$Kota['kota']."'
				WHERE id = '".$Kota['KotaID']."'
				LIMIT 1
			";
			$UpdateResult = mysql_query($UpdateQuery) or die(mysql_error());
			
			$Result['KotaID'] = $Kota['KotaID'];
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data berhasil diperbaharui.';
		}
		
		return $Result;
	}
	
	function GetByID($Param) {
		$Array = array();
		
		if (isset($Param['KotaID'])) {
			$SelectQuery  = "SELECT * FROM ".KOTA." WHERE id = '".$Param['KotaID']."' LIMIT 1";
		}
		
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		if (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$Array = StripArray($Row);
		}
		
		return $Array;
	}
	
	function GetArray($Param = array()) {
		$Array = array();
		$StringSearch = (isset($Param['NameLike'])) ? "AND kota LIKE '" . $Param['NameLike'] . "%'"  : '';
		$StringPropinsi = (isset($Param['idpropinsi']) && !empty($Param['idpropinsi'])) ? "AND idpropinsi = '" . $Param['idpropinsi'] . "'"  : '';
		$StringFilter = GetStringFilter($Param);
		
		$PageOffset = (isset($Param['start']) && !empty($Param['start'])) ? $Param['start'] : 0;
		$PageLimit = (isset($Param['limit']) && !empty($Param['limit'])) ? $Param['limit'] : 25;
		
		$SortingTemp = (isset($Param['sort']) && !empty($Param['sort'])) ? json_decode($Param['sort']) : 'kota';
		$Sorting = (is_array($SortingTemp)) ? $SortingTemp[0]->property : $SortingTemp;
		$Ordering = (is_array($SortingTemp)) ? $SortingTemp[0]->direction : 'ASC';
		
		$SelectQuery = "
			SELECT Kota.*
			FROM ".KOTA." Kota
			WHERE 1 $StringSearch $StringPropinsi $StringFilter
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
		
		$StringSearch = (isset($Param['NameLike'])) ? "AND kota LIKE '" . $Param['NameLike'] . "%'"  : '';
		$StringPropinsi = (isset($Param['idpropinsi'])) ? "AND idpropinsi = '" . $Param['idpropinsi'] . "'"  : '';
		$StringFilter = GetStringFilter($Param);
		
		$SelectQuery = "
			SELECT COUNT(*) AS TotalRecord
			FROM ".KOTA." Kota
			WHERE 1 $StringSearch $StringPropinsi $StringFilter
		";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$TotalRecord = $Row['TotalRecord'];
		}
		
		return $TotalRecord;
	}
	
	function Delete($Param) {
        $DeleteQuery  = "DELETE FROM ".KOTA." WHERE id = '".$Param['KotaID']."' LIMIT 1";
        $DeleteResult = mysql_query($DeleteQuery) or die(mysql_error());
        
        $Result['QueryStatus'] = '1';
        $Result['Message'] = 'Data berhasil dihapus.';
		
		return $Result;
	}
}