<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Permission extends CI_Model {
	function __construct() {
        parent::__construct();
    }
	
	function Update($Param) {
		if ($Param['IsInsert'] == 1) {
			$InsertQuery  = "INSERT INTO ".GROUP_PERMISSION." (group_id, perm_id) VALUES ('".$Param['group_id']."', '".$Param['perm_id']."')";
			$InsertResult = mysql_query($InsertQuery) or die(mysql_error());
		} else {
			$DeleteQuery = "DELETE FROM ".GROUP_PERMISSION." WHERE group_id = '".$Param['group_id']."' AND perm_id = '".$Param['perm_id']."' LIMIT 1";
			$DeleteResult = mysql_query($DeleteQuery) or die(mysql_error());
		}
	}
	
	function GetAccessGerejaID($Param) {
		$GerejaID = 0;
		if ($Param['group_id'] == 1) {
			$GerejaID = 0;
		} else if ($Param['group_id'] == 4) {
			$GerejaID = (empty($Param['gereja_id'])) ? -1 : $Param['gereja_id'];
		}
		
		return $GerejaID;
	}
	
	function GetArray($Param = array()) {
		$Array = array();
		$StringSearch = (isset($Param['NameLike'])) ? "AND nama LIKE '" . $Param['NameLike'] . "%'"  : '';
		$StringFilter = GetStringFilter($Param);
		
		$PageOffset = (isset($Param['start']) && !empty($Param['start'])) ? $Param['start'] : 0;
		$PageLimit = (isset($Param['limit']) && !empty($Param['limit'])) ? $Param['limit'] : 25;
		
		$SortingTemp = (isset($Param['sort']) && !empty($Param['sort'])) ? json_decode($Param['sort']) : 'nama';
		$Sorting = (is_array($SortingTemp)) ? $SortingTemp[0]->property : $SortingTemp;
		$Ordering = (is_array($SortingTemp)) ? $SortingTemp[0]->direction : 'ASC';
		
		$SelectQuery = "
			SELECT Permission.*
			FROM ".PERMISSION." Permission
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
		
		$StringSearch = (isset($Param['NameLike'])) ? "AND nama LIKE '" . $Param['NameLike'] . "%'"  : '';
		$StringFilter = GetStringFilter($Param);
		
		$SelectQuery = "
			SELECT COUNT(*) AS TotalRecord
			FROM ".PERMISSION." Permission
			WHERE 1 $StringSearch $StringFilter
		";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$TotalRecord = $Row['TotalRecord'];
		}
		
		return $TotalRecord;
	}
	
	function GetArrayGroup($Param = array()) {
		$Array = array();
		$StringGroup = (isset($Param['GroupID'])) ? "AND group_id = '" . $Param['GroupID'] . "'"  : '';
		$StringSearch = (isset($Param['NameLike'])) ? "AND nama LIKE '" . $Param['NameLike'] . "%'"  : '';
		$StringFilter = GetStringFilter($Param);
		
		$SelectQuery = "
			SELECT *
			FROM ".GROUP_PERMISSION."
			WHERE 1 $StringGroup $StringSearch $StringFilter
		";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$Row = StripArray($Row);
			$Array[] = $Row;
		}
		
		return $Array;
	}
	
	function GetCollection($Param) {
		$Param['SingleData'] = (isset($Param['SingleData'])) ? $Param['SingleData'] : '';
		
		$UserPermission = $this->GetArrayGroup($Param);
		
		$ArrayPermission[] = array( 'Group' => 'Informasi', 'ModulName' => 'Gereja', 'Read' => 0, 'ReadID' => 5, 'Write' => 0, 'WriteID' => 6, 'Link' => '/administrator/entry/gereja', 'Title' => 'Gereja' );
		$ArrayPermission[] = array( 'Group' => 'Informasi', 'ModulName' => 'Jemaat', 'Read' => 0, 'ReadID' => 1, 'Write' => 0, 'WriteID' => 2, 'Link' => '/administrator/entry/jemaat', 'Title' => 'Jemaat' );
		$ArrayPermission[] = array( 'Group' => 'Informasi', 'ModulName' => 'Keluarga', 'Read' => 0, 'ReadID' => 3, 'Write' => 0, 'WriteID' => 4, 'Link' => '/administrator/entry/keluarga', 'Title' => 'Keluarga' );
		$ArrayPermission[] = array( 'Group' => 'Informasi', 'ModulName' => 'Sektor', 'Read' => 0, 'ReadID' => 23, 'Write' => 0, 'WriteID' => 24, 'Link' => '/keluarga/sektor', 'Title' => 'Sektor' );
		
		$ArrayPermission[] = array( 'Group' => 'Keuangan', 'ModulName' => 'MetodePengiriman', 'Read' => 0, 'ReadID' => 25, 'Write' => 0, 'WriteID' => 26, 'Link' => '/finance/metode_kirim', 'Title' => 'Metode Pengiriman' );
		$ArrayPermission[] = array( 'Group' => 'Keuangan', 'ModulName' => 'JenisBiaya', 'Read' => 0, 'ReadID' => 27, 'Write' => 0, 'WriteID' => 28, 'Link' => '/finance/jenis_biaya', 'Title' => 'Jenis Biaya' );
		$ArrayPermission[] = array( 'Group' => 'Keuangan', 'ModulName' => 'Tagihan', 'Read' => 0, 'ReadID' => 19, 'Write' => 0, 'WriteID' => 20, 'Link' => '/finance/bill', 'Title' => 'Tagihan' );
		$ArrayPermission[] = array( 'Group' => 'Keuangan', 'ModulName' => 'Pembayaran', 'Read' => 0, 'ReadID' => 21, 'Write' => 0, 'WriteID' => 22, 'Link' => '/finance/payment', 'Title' => 'Pembayaran' );
		$ArrayPermission[] = array( 'Group' => 'Keuangan', 'ModulName' => 'Pendanaan', 'Read' => 0, 'ReadID' => 29, 'Write' => 0, 'WriteID' => 30, 'Link' => '/finance/pendanaan', 'Title' => 'Pendanaan' );
		
		$ArrayPermission[] = array( 'Group' => 'Administrasi', 'ModulName' => 'User', 'Read' => 0, 'ReadID' => 7, 'Write' => 0, 'WriteID' => 8, 'Link' => '/administrator/entry/user', 'Title' => 'User' );
		$ArrayPermission[] = array( 'Group' => 'Administrasi', 'ModulName' => 'PermissionGroup', 'Read' => 0, 'ReadID' => 9, 'Write' => 0, 'WriteID' => 10, 'Link' => '/administrator/entry/group', 'Title' => 'Permission Group' );
		
		$ArrayPermission[] = array( 'Group' => 'Master', 'ModulName' => 'Profesi', 'Read' => 0, 'ReadID' => 11, 'Write' => 0, 'WriteID' => 12, 'Link' => '/administrator/entry/profesi', 'Title' => 'Profesi' );
		$ArrayPermission[] = array( 'Group' => 'Master', 'ModulName' => 'Pendidikan', 'Read' => 0, 'ReadID' => 13, 'Write' => 0, 'WriteID' => 14, 'Link' => '/administrator/entry/pendidikan', 'Title' => 'Pendidikan' );
		$ArrayPermission[] = array( 'Group' => 'Master', 'ModulName' => 'SiteConfig', 'Read' => 0, 'ReadID' => 15, 'Write' => 0, 'WriteID' => 16, 'Link' => '/site/config', 'Title' => 'Site Config' );
		$ArrayPermission[] = array( 'Group' => 'Master', 'ModulName' => 'Log', 'Read' => 0, 'ReadID' => 17, 'Write' => 0, 'WriteID' => 18, 'Link' => '/site/log', 'Title' => 'Log' );
		
		foreach ($UserPermission as $Array) {
			foreach ($ArrayPermission as $Key => $Permission) {
				if ($Permission['ReadID'] == $Array['perm_id']) {
					$ArrayPermission[$Key]['Read'] = 1;
					break;
				} else if ($Permission['WriteID'] == $Array['perm_id']) {
					$ArrayPermission[$Key]['Write'] = 1;
					break;
				}
			}
		}
		
		if (!empty($Param['SingleData'])) {
			foreach ($ArrayPermission as $Key => $Array) {
				if ($Array['ModulName'] == $Param['SingleData']) {
					$ArrayResult = $Array;
					break;
				}
			}
		} else {
			$ArrayResult['PermissionData'] = $ArrayPermission;
			$ArrayResult['PermissionCount'] = count($ArrayPermission);
		}
		
		return $ArrayResult;
	}
}