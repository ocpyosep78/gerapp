<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Log extends CI_Model {
	function __construct() {
        parent::__construct();
		
		$this->Field = array('log_id', 'user_id', 'gereja_id', 'log_datetime', 'log_action', 'log_query');
    }
	
	function Update($Param) {
		$Result = array();
		
		if (empty($Param['log_id'])) {
			$InsertQuery  = GenerateInsertQuery($this->Field, $Param, LOG);
			$InsertResult = mysql_query($InsertQuery) or die(mysql_error());
			
			$Result['log_id'] = mysql_insert_id();
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data successfully stored.';
		} else {
			$UpdateQuery  = GenerateUpdateQuery($this->Field, $Param, LOG);
			$UpdateResult = mysql_query($UpdateQuery) or die(mysql_error());
			
			$Result['log_id'] = $Param['log_id'];
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data successfully updated.';
		}
		
		return $Result;
	}
	
	function Write($Message, $Query) {
		$User = $this->M_User->GetCurrentUser();
		
		$ParamLog['user_id'] = $User['id'];
		$ParamLog['gereja_id'] = $User['gereja_id'];
		$ParamLog['log_datetime'] = date($this->config->item('log_date_format'));
		$ParamLog['log_action'] = $Message;
		$ParamLog['log_query'] = $Query;
		$this->Update($ParamLog);
	}
	
	function GetByID($Param) {
		$Array = array();
		
		if (isset($Param['log_id'])) {
			$SelectQuery  = "SELECT * FROM ".LOG." WHERE log_id = '".$Param['log_id']."' LIMIT 1";
		}
		
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		if (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$Array = StripArray($Row);
		}
		
		return $Array;
	}
	
	function GetArray($Param = array()) {
		$Array = array();
		$StringSearch = (isset($Param['NameLike'])) ? "AND log LIKE '%" . $Param['NameLike'] . "%'"  : '';
		$StringFilter = GetStringFilter($Param);
		
		$PageOffset = (isset($Param['start']) && !empty($Param['start'])) ? $Param['start'] : 0;
		$PageLimit = (isset($Param['limit']) && !empty($Param['limit'])) ? $Param['limit'] : 25;
        $StringSorting = (isset($Param['sort'])) ? GetStringSorting($Param['sort']) : 'log ASC';
		
		$SelectQuery = "
			SELECT Log.*, User.username UserName, Gereja.nama GerejaName
			FROM ".LOG." Log
			LEFT JOIN ".USER." User ON User.id = Log.user_id
			LEFT JOIN ".GEREJA." Gereja ON Gereja.id = Log.gereja_id
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
		
		$StringSearch = (isset($Param['NameLike'])) ? "AND log LIKE '%" . $Param['NameLike'] . "%'"  : '';
		$StringFilter = GetStringFilter($Param);
		
		$SelectQuery = "
			SELECT COUNT(*) AS TotalRecord
			FROM ".LOG." Log
			WHERE 1 $StringSearch $StringFilter
		";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$TotalRecord = $Row['TotalRecord'];
		}
		
		return $TotalRecord;
	}
	
	function Delete($Param) {
        if (isset($Param['list_log_id'])) {
			$DeleteQuery  = "DELETE FROM ".LOG." WHERE log_id IN (".$Param['list_log_id'].")";
			$DeleteResult = mysql_query($DeleteQuery) or die(mysql_error());
		} else if (isset($Param['log_id'])) {
			$DeleteQuery  = "DELETE FROM ".LOG." WHERE log_id = '".$Param['log_id']."' LIMIT 1";
			$DeleteResult = mysql_query($DeleteQuery) or die(mysql_error());
        }
		
        $Result['QueryStatus'] = '1';
        $Result['Message'] = 'Data has been deleted.';
		
		return $Result;
	}
}