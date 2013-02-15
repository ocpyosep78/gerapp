<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Sektor extends CI_Model {
	function __construct() {
        parent::__construct();
		
		$this->Field = array('sektor_id', 'gereja_id', 'parent_id', 'sektor');
    }
	
	function SetDefault($Param) {
		if (empty($Param['gereja_id'])) {
			return;
		}
		
		$Count = $this->GetCount($Param);
		if ($Count == 0) {
			$Gereja = $this->M_Gereja->GetByID(array('GerejaID' => $Param['gereja_id']));
			$ParamInsert['gereja_id'] = $Gereja['id'];
			$ParamInsert['sektor'] = $Gereja['nama'];
			$this->Update($ParamInsert);
		}
	}
	
	function Update($Param) {
		$Result = array();
		
		if (empty($Param['sektor_id'])) {
			$InsertQuery  = GenerateInsertQuery($this->Field, $Param, SEKTOR);
			$InsertResult = mysql_query($InsertQuery) or die(mysql_error());
			
			$Result['sektor_id'] = mysql_insert_id();
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data berhasil tersimpan.';
			
			$this->M_Log->Write('Menambah Sektor (' . $Result['sektor_id'] . ')', $InsertQuery);
		} else {
			$UpdateQuery  = GenerateUpdateQuery($this->Field, $Param, SEKTOR);
			$UpdateResult = mysql_query($UpdateQuery) or die(mysql_error());
			
			$Result['sektor_id'] = $Param['sektor_id'];
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data berhasil diperbaharui.';
			
			$this->M_Log->Write('Mengubah Sektor (' . $Result['sektor_id'] . ')', $UpdateQuery);
		}
		
		return $Result;
	}
	
	function GetByID($Param) {
		$Array = array();
		
		if (isset($Param['sektor_id'])) {
			$SelectQuery  = "
				SELECT Sektor.*
				FROM ".SEKTOR." Sektor
				WHERE Sektor.sektor_id = '".$Param['sektor_id']."' LIMIT 1";
		}
		
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		if (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$Array = StripArray($Row);
		}
		
		return $Array;
	}
	
	function GetArray($Param = array()) {
		$Param['gereja_id'] = (isset($Param['gereja_id'])) ? $Param['gereja_id'] : 0;
		
		$Array = array();
		$StringParent = (isset($Param['parent_id'])) ? "AND parent_id = '" . $Param['parent_id'] . "'"  : '';
		$StringGereja = (!empty($Param['gereja_id'])) ? "AND gereja_id = '" . $Param['gereja_id'] . "'"  : '';
		$StringFilter = GetStringFilter($Param);
		$StringSorting = (isset($Param['sort'])) ? GetStringSorting($Param['sort']) : 'sektor ASC';
		
		$SelectQuery = "
			SELECT Sektor.*
			FROM ".SEKTOR." Sektor
			WHERE 1 $StringParent $StringGereja $StringFilter
			ORDER BY $StringSorting
		";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$Row = StripArray($Row);
			$Row['id'] = $Row['sektor_id'];
			$Row['text'] = $Row['sektor'];
			
			$ArrayChild = $this->GetArray(array('parent_id' => $Row['sektor_id'], 'gereja_id' => $Param['gereja_id']));
			if (count($ArrayChild) > 0) {
				$Row['expanded'] = true;
				$Row['children'] = $ArrayChild;
			} else {
				$Row['leaf'] = true;
			}
			
			$Array[] = $Row;
		}
		
		return $Array;
	}
	
	function GetCount($Param = array()) {
		$TotalRecord = 0;
		
		$StringGerejaID = (isset($Param['gereja_id'])) ? "AND gereja_id = '" . $Param['gereja_id'] . "'"  : '';
		$StringFilter = GetStringFilter($Param);
		
		$SelectQuery = "
			SELECT COUNT(*) AS TotalRecord
			FROM ".SEKTOR." Sektor
			WHERE 1 $StringGerejaID $StringFilter
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
		$SelectQuery[] = "SELECT COUNT(*) RecordCount FROM ".PENDANAAN." WHERE sektor_id = '".$Param['sektor_id']."'";
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
		
		$DeleteQuery  = "DELETE FROM ".SEKTOR." WHERE sektor_id = '".$Param['sektor_id']."' LIMIT 1";
		$DeleteResult = mysql_query($DeleteQuery) or die(mysql_error());
		
		$Result['QueryStatus'] = '1';
		$Result['Message'] = 'Data berhasil dihapus.';
		
		$this->M_Log->Write('Menghapus Sektor (' . $Param['sektor_id'] . ')', $DeleteQuery);
		
		return $Result;
	}
}