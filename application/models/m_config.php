<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Config extends CI_Model {
	function __construct() {
        parent::__construct();
		
		$this->Field = array('config_id', 'gereja_id', 'config_name', 'config', 'hidden');
		$this->DefaultData = array(
			array( 'config_name' => 'Site Header', 'config' => SITE_HEADER, 'hidden' => 0 ),
			array( 'config_name' => 'Site Footer', 'config' => SITE_FOOTER, 'hidden' => 0 ),
			array( 'config_name' => 'Iuran Anak', 'config' => IURAN_ANAK, 'hidden' => 0 ),
			array( 'config_name' => 'Iuran Dewasa', 'config' => IURAN_DEWASA, 'hidden' => 0 ),
			array( 'config_name' => 'Logo Gereja', 'config' => LOGO_GEREJA, 'hidden' => 1 )
		);
    }
	
	function CheckDefault($Param) {
		if (empty($Param['gereja_id'])) {
			return;
		}
		
		foreach ($this->DefaultData as $Key => $Array) {
			$Config = $this->GetByID(array('config_name' => $Array['config_name'], 'gereja_id' => $Param['gereja_id']));
			if (count($Config) == 0) {
				$Array['gereja_id'] = $Param['gereja_id'];
				$this->Update($Array);
			}
		}
	}
	
	function Update($Param) {
		$Result = array();
		
		if (empty($Param['config_id'])) {
			$InsertQuery  = GenerateInsertQuery($this->Field, $Param, CONFIG, array('AllowSymbol' => 0));
			$InsertResult = mysql_query($InsertQuery) or die(mysql_error());
			
			$Result['config_id'] = mysql_insert_id();
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data successfully stored.';
		} else {
			$UpdateQuery  = GenerateUpdateQuery($this->Field, $Param, CONFIG, array('AllowSymbol' => 0));
			$UpdateResult = mysql_query($UpdateQuery) or die(mysql_error());
			
			$Result['config_id'] = $Param['config_id'];
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data successfully updated.';
		}
		
		return $Result;
	}
	
	function AddGereja($Param) {
		$Param = array( 'gereja_id' => $Param['gereja_id'], 'config_name' => 'Site Header', 'config' => SITE_HEADER, 'hidden' => 0 );
		$this->Update($Param);
		
		$Param = array( 'gereja_id' => $Param['gereja_id'], 'config_name' => 'Site Footer', 'config' => SITE_FOOTER, 'hidden' => 0 );
		$this->Update($Param);
		
		$Param = array( 'gereja_id' => $Param['gereja_id'], 'config_name' => 'Iuran Anak', 'config' => IURAN_ANAK, 'hidden' => 0 );
		$this->Update($Param);
		
		$Param = array( 'gereja_id' => $Param['gereja_id'], 'config_name' => 'Iuran Dewasa', 'config' => IURAN_DEWASA, 'hidden' => 0 );
		$this->Update($Param);
		
		$Param = array( 'gereja_id' => $Param['gereja_id'], 'config_name' => 'Logo Gereja', 'config' => LOGO_GEREJA, 'hidden' => 1 );
		$this->Update($Param);
	}
	
	function GetByID($Param) {
		$Array = array();
		
		if (isset($Param['config_id'])) {
			$SelectQuery  = "
				SELECT Config.*
				FROM ".CONFIG." Config
				WHERE config_id = '".$Param['config_id']."'
				LIMIT 1";
		} else if (isset($Param['config_name']) && isset($Param['gereja_id'])) {
			$SelectQuery  = "
				SELECT Config.*
				FROM ".CONFIG." Config
				WHERE
					config_name = '".$Param['config_name']."'
					AND gereja_id = '".$Param['gereja_id']."'
				LIMIT 1
			";
		} else if (isset($Param['config_name'])) {
			$SelectQuery  = "
				SELECT Config.*
				FROM ".CONFIG." Config
				WHERE config_name = '".$Param['config_name']."'
				LIMIT 1";
		}
		
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		if (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$Array = StripArray($Row);
		}
		
		return $Array;
	}
	
	function GetArray($Param = array()) {
		$Array = array();
		$StringSearch = (isset($Param['NameLike'])) ? "AND config_name LIKE '%" . $Param['NameLike'] . "%'"  : '';
		$StringHidden = (isset($Param['hidden'])) ? "AND hidden = '" . $Param['hidden'] . "'"  : '';
		$StringGerejaID = (isset($Param['gereja_id'])) ? "AND gereja_id = '" . $Param['gereja_id'] . "'"  : '';
		$StringFilter = GetStringFilter($Param, array('gereja' => 'Gereja.nama'));
		
		$PageOffset = (isset($Param['start']) && !empty($Param['start'])) ? $Param['start'] : 0;
		$PageLimit = (isset($Param['limit']) && !empty($Param['limit'])) ? $Param['limit'] : 25;
        $StringSorting = (isset($Param['sort'])) ? GetStringSorting($Param['sort']) : 'config_name ASC';
		
		$SelectQuery = "
			SELECT Config.*, Gereja.nama gereja
			FROM ".CONFIG." Config
			LEFT JOIN ".GEREJA." Gereja ON Gereja.id = Config.gereja_id
			WHERE 1 $StringSearch $StringHidden $StringGerejaID $StringFilter
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
		
		$StringSearch = (isset($Param['NameLike'])) ? "AND config_name LIKE '%" . $Param['NameLike'] . "%'"  : '';
		$StringHidden = (isset($Param['hidden'])) ? "AND hidden = '" . $Param['hidden'] . "'"  : '';
		$StringGerejaID = (isset($Param['gereja_id'])) ? "AND gereja_id = '" . $Param['gereja_id'] . "'"  : '';
		$StringFilter = GetStringFilter($Param, array('gereja' => 'Gereja.nama'));
		
		$SelectQuery = "
			SELECT COUNT(*) AS TotalRecord
			FROM ".CONFIG." Config
			LEFT JOIN ".GEREJA." Gereja ON Gereja.id = Config.gereja_id
			WHERE 1 $StringSearch $StringHidden $StringGerejaID $StringFilter
		";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$TotalRecord = $Row['TotalRecord'];
		}
		
		return $TotalRecord;
	}
	
	function Delete($Param) {
		if (isset($Param['list_config_id'])) {
			$DeleteQuery  = "DELETE FROM ".CONFIG." WHERE config_id IN (".$Param['list_config_id'].")";
			$DeleteResult = mysql_query($DeleteQuery) or die(mysql_error());
		} else if (isset($Param['config_id'])) {
			$DeleteQuery  = "DELETE FROM ".CONFIG." WHERE config_id = '".$Param['config_id']."' LIMIT 1";
			$DeleteResult = mysql_query($DeleteQuery) or die(mysql_error());
		}
        
        $Result['QueryStatus'] = '1';
        $Result['Message'] = 'Data has been deleted.';
		
		return $Result;
	}
	
	function DeleteGereja($Param) {
		$DeleteQuery  = "DELETE FROM ".CONFIG." WHERE gereja_id = '" . $Param['gereja_id'] . "'";
		$DeleteResult = mysql_query($DeleteQuery) or die(mysql_error());
	}
}