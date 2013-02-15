<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Jemaat_Rekap extends CI_Model {
	function __construct() {
        parent::__construct();
		
		$this->Field = array('jemaat_rekap_id', 'jemaat_rekap_date', 'jemaat_rekap_total');
    }
	
	function Update($Param) {
		$Result = array();
		
		if (empty($Param['jemaat_rekap_id'])) {
			$InsertQuery  = GenerateInsertQuery($this->Field, $Param, JEMAAT_REKAP);
			$InsertResult = mysql_query($InsertQuery) or die(mysql_error());
			
			$Result['jemaat_rekap_id'] = mysql_insert_id();
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data successfully stored.';
		} else {
			$UpdateQuery  = GenerateUpdateQuery($this->Field, $Param, JEMAAT_REKAP);
			$UpdateResult = mysql_query($UpdateQuery) or die(mysql_error());
			
			$Result['jemaat_rekap_id'] = $Param['jemaat_rekap_id'];
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data successfully updated.';
		}
		
		return $Result;
	}
	
	function GetByID($Param) {
		$Array = array();
		
		if (isset($Param['jemaat_rekap_id'])) {
			$SelectQuery  = "SELECT JemaatRekap.* FROM ".JEMAAT_REKAP." JemaatRekap WHERE jemaat_rekap_id = '".$Param['jemaat_rekap_id']."' LIMIT 1";
		}
		
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		if (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$Array = StripArray($Row);
		}
		
		return $Array;
	}
	
	function GetArray($Param = array()) {
		$Array = array();
		
		$StringMonth = (isset($Param['Month'])) ? GetStringMonth($Param['Month']) : '';
		$StringListID = (isset($Param['StringListID'])) ? "AND jemaat_rekap_id IN (" . $Param['StringListID'] . ")" : '';
		$StringFilter = GetStringFilter($Param);
		
		$PageOffset = (isset($Param['start']) && !empty($Param['start'])) ? $Param['start'] : 0;
		$PageLimit = (isset($Param['limit']) && !empty($Param['limit'])) ? $Param['limit'] : 25;
        $StringSorting = (isset($Param['sort'])) ? GetStringSorting($Param['sort']) : 'jemaat_rekap_date ASC';
		
		$SelectQuery = "
			SELECT JemaatRekap.*
			FROM ".JEMAAT_REKAP." JemaatRekap
			WHERE 1 $StringMonth $StringListID $StringFilter
			ORDER BY $StringSorting
			LIMIT $PageOffset, $PageLimit
		";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$Row = StripArray($Row);
			$Row['name'] = GetFormatDateCommon($Row['jemaat_rekap_date'], array('FormatDate' => 'F Y'));
			$Row['jemaat_rekap_total'] = intval($Row['jemaat_rekap_total']);
			$Array[] = $Row;
		}
		
		return $Array;
	}
	
	function GetCount($Param = array()) {
		$TotalRecord = 0;
		
		$StringMonth = (isset($Param['Month'])) ? GetStringMonth($Param['Month']) : '';
		$StringListID = (isset($Param['StringListID'])) ? "AND jemaat_rekap_id IN (" . $Param['StringListID'] . ")" : '';
		$StringFilter = GetStringFilter($Param);
		
		$SelectQuery = "
			SELECT COUNT(*) AS TotalRecord
			FROM ".JEMAAT_REKAP." JemaatRekap
			WHERE 1 $StringMonth $StringListID $StringFilter
		";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$TotalRecord = $Row['TotalRecord'];
		}
		
		return $TotalRecord;
	}
	
	function GetChartLine() {
		$StringListID = '';
		$ArrayTemp = $this->GetArray(array('limit' => 6, 'sort' => '[{"property":"jemaat_rekap_date","direction":"DESC"}]'));
		foreach ($ArrayTemp as $Key => $Array) {
			$StringListID .= (empty($StringListID)) ? "'" . $Array['jemaat_rekap_id'] . "'" : ",'" . $Array['jemaat_rekap_id'] . "'";
		}
		
		$ArrayResult = $this->GetArray(array('StringListID' => $StringListID));
		return $ArrayResult;
	}
	
	function Delete($Param) {
		if (isset($Param['list_jemaat_rekap_id'])) {
			$DeleteQuery  = "DELETE FROM ".JEMAAT_REKAP." WHERE jemaat_rekap_id IN (".$Param['list_jemaat_rekap_id'].")";
			$DeleteResult = mysql_query($DeleteQuery) or die(mysql_error());
		} else if (isset($Param['jemaat_rekap_id'])) {
			$DeleteQuery  = "DELETE FROM ".JEMAAT_REKAP." WHERE jemaat_rekap_id = '".$Param['jemaat_rekap_id']."' LIMIT 1";
			$DeleteResult = mysql_query($DeleteQuery) or die(mysql_error());
		}
        
        $Result['QueryStatus'] = '1';
        $Result['Message'] = 'Data has been deleted.';
		
		return $Result;
	}
	
	function CheckMonthly() {
		$ArrayParam['Month'] = array( 'field' => 'jemaat_rekap_date', 'value' => date('m') );
		$ArrayMonth = $this->GetArray($ArrayParam);
		
		if (count($ArrayMonth) == 0) {
			$ParamUpdate = array(
				'jemaat_rekap_id' => 0,
				'jemaat_rekap_date' => date('Y-m-d'),
				'jemaat_rekap_total' => $this->M_Jemaat->GetCount()
			);
			$this->Update($ParamUpdate);
		}
	}
}