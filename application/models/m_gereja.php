<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Gereja extends CI_Model {
	function __construct() {
        parent::__construct();
		
		$this->Field = array('id', 'idkota', 'idpropinsi', 'idnegara', 'nama', 'alamat');
    }
	
	function Update($Param) {
		$Result = array();
		$Param['id'] = $Param['GerejaID'];
		
		if (empty($Param['GerejaID'])) {
			$InsertQuery  = GenerateInsertQuery($this->Field, $Param, GEREJA);
			$InsertResult = mysql_query($InsertQuery) or die(mysql_error());
			
			$Result['GerejaID'] = mysql_insert_id();
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data berhasil tersimpan.';
			
			$this->M_Config->AddGereja(array('gereja_id' => $Result['GerejaID']));
			$this->M_Log->Write('Menambah Gereja (' . $Result['GerejaID'] . ')', $InsertQuery);
		} else {
			$UpdateQuery  = GenerateUpdateQuery($this->Field, $Param, GEREJA);
			$UpdateResult = mysql_query($UpdateQuery) or die(mysql_error());
			
			$Result['GerejaID'] = $Param['GerejaID'];
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data berhasil diperbaharui.';
			
			$this->M_Log->Write('Mengubah Gereja (' . $Result['GerejaID'] . ')', $UpdateQuery);
		}
		
		return $Result;
	}
	
	function UpdateLogo($Param) {
		$UpdateQuery  = "
			UPDATE ".GEREJA." SET logo = '".$Param['logo']."'
			WHERE id = '".$Param['GerejaID']."'
			LIMIT 1
		";
		$UpdateResult = mysql_query($UpdateQuery) or die(mysql_error());
		
		$Result['QueryStatus'] = '1';
		$Result['Message'] = 'Data berhasil diperbaharui.';
		
		return $Result;
	}
	
	function UpdateAdmin($Param) {
		$DeleteQuery  = "DELETE FROM ".USER_GEREJA." WHERE gereja_id = '".$Param['GerejaID']."'";
		$DeleteResult = mysql_query($DeleteQuery) or die(mysql_error());
		
		$InsertQuery  = "INSERT INTO ".USER_GEREJA." (user_id, gereja_id) VALUES ('".$Param['UserID']."', '".$Param['GerejaID']."')";
		$InsertResult = mysql_query($InsertQuery) or die(mysql_error());
	}
	
	function GetByID($Param) {
		$Array = array();
		
		if (isset($Param['GerejaID'])) {
			$SelectQuery  = "
				SELECT Gereja.*, User.id UserID, User.name, Kota.kota, Propinsi.propinsi, Negara.negara
				FROM ".GEREJA." Gereja
				LEFT JOIN ".KOTA." Kota ON Gereja.idkota = Kota.id
				LEFT JOIN ".PROPINSI." Propinsi ON Gereja.idpropinsi = Propinsi.id
				LEFT JOIN ".NEGARA." Negara ON Gereja.idnegara = Negara.id
				LEFT JOIN ".USER_GEREJA." UserGereja ON Gereja.id = UserGereja.gereja_id
				LEFT JOIN ".USER." User ON UserGereja.user_id = User.id
				WHERE Gereja.id = '".$Param['GerejaID']."' LIMIT 1";
		}
		
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		if (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$Array = StripArray($Row);
		}
		
		return $Array;
	}
	
	function GetArray($Param = array()) {
		$Array = array();
		$ForceDisplayID = (isset($Param['ForceDisplayID'])) ? $Param['ForceDisplayID'] : 0;
		$StringSearch = (isset($Param['NameLike'])) ? "AND nama LIKE '" . $Param['NameLike'] . "%'"  : '';
		$StringGerejaID = (isset($Param['id']) && !empty($Param['id'])) ? "AND Gereja.id = '" . $Param['id'] . "'"  : '';
		$StringFilter = GetStringFilter($Param);
		
		$PageOffset = (isset($Param['start']) && !empty($Param['start'])) ? $Param['start'] : 0;
		$PageLimit = (isset($Param['limit']) && !empty($Param['limit'])) ? $Param['limit'] : 25;
		
		$SortingTemp = (isset($Param['sort']) && !empty($Param['sort'])) ? json_decode($Param['sort']) : 'nama';
		$Sorting = (is_array($SortingTemp)) ? $SortingTemp[0]->property : $SortingTemp;
		$Ordering = (is_array($SortingTemp)) ? $SortingTemp[0]->direction : 'ASC';
		
		$SelectQuery = "
			SELECT Gereja.*, User.name, Kota.kota, Propinsi.propinsi, Negara.negara
			FROM ".GEREJA." Gereja
			LEFT JOIN ".KOTA." Kota ON Gereja.idkota = Kota.id
			LEFT JOIN ".PROPINSI." Propinsi ON Gereja.idpropinsi = Propinsi.id
			LEFT JOIN ".NEGARA." Negara ON Gereja.idnegara = Negara.id
			LEFT JOIN ".USER_GEREJA." UserGereja ON Gereja.id = UserGereja.gereja_id
			LEFT JOIN ".USER." User ON UserGereja.user_id = User.id
			WHERE 1 $StringSearch $StringGerejaID $StringFilter
			ORDER BY $Sorting $Ordering
			LIMIT $PageOffset, $PageLimit
		";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
            if (!empty($ForceDisplayID)) {
                $ForceDisplayID = ($ForceDisplayID == $Row['id']) ? 0 : $ForceDisplayID;
            }
           
			$Row = StripArray($Row);
			$Array[] = $Row;
		}
		
        if (!empty($ForceDisplayID)) {
            $ArrayForce = $this->GetByID(array('id' => $ForceDisplayID));
			if (count($ArrayForce) > 0) {
				$Array[] = $ArrayForce;
			}
        }
		
		return $Array;
	}
	
	function GetCount($Param = array()) {
		$TotalRecord = 0;
		
		$StringSearch = (isset($Param['NameLike'])) ? "AND nama LIKE '" . $Param['NameLike'] . "%'"  : '';
		$StringGerejaID = (isset($Param['id']) && !empty($Param['id'])) ? "AND Gereja.id = '" . $Param['id'] . "'"  : '';
		$StringFilter = GetStringFilter($Param);
		
		$SelectQuery = "
			SELECT COUNT(*) AS TotalRecord
			FROM ".GEREJA." Gereja
			LEFT JOIN ".USER_GEREJA." UserGereja ON Gereja.id = UserGereja.gereja_id
			LEFT JOIN ".USER." User ON UserGereja.user_id = User.id
			WHERE 1 $StringSearch $StringGerejaID $StringFilter
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
		$SelectQuery[] = "SELECT COUNT(*) RecordCount FROM ".JEMAAT." WHERE idgereja = '".$Param['GerejaID']."'";
		$SelectQuery[] = "SELECT COUNT(*) RecordCount FROM ".KELUARGA." WHERE idgereja = '".$Param['GerejaID']."'";
		$SelectQuery[] = "SELECT COUNT(*) RecordCount FROM ".SEKTOR." WHERE gereja_id = '".$Param['GerejaID']."'";
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
		
		$DeleteQuery  = "DELETE FROM ".USER_GEREJA." WHERE gereja_id = '".$Param['GerejaID']."' LIMIT 1";
		$DeleteResult = mysql_query($DeleteQuery) or die(mysql_error());
		
		$DeleteQuery  = "DELETE FROM ".GEREJA." WHERE id = '".$Param['GerejaID']."' LIMIT 1";
		$DeleteResult = mysql_query($DeleteQuery) or die(mysql_error());
		
		$Result['QueryStatus'] = '1';
		$Result['Message'] = 'Data berhasil dihapus.';
		
		$this->M_Config->DeleteGereja(array('gereja_id' => $Param['GerejaID']));
		$this->M_Log->Write('Menghapus Gereja (' . $Param['GerejaID'] . ')', $DeleteQuery);
		
		return $Result;
	}
}