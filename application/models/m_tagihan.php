<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Tagihan extends CI_Model {
	function __construct() {
        parent::__construct();
		
		$this->Field = array('tagihan_id', 'jemaat_id', 'tagihan_type_id', 'tagihan_tanggal', 'tagihan_note', 'tagihan_nilai', 'tagihan_bayar', 'InsertBy', 'UpdateBy', 'InsertTime', 'UpdateTime');
    }
	
	function Update($Param) {
		$Result = array();
		
		if (empty($Param['tagihan_id'])) {
			$InsertQuery  = GenerateInsertQuery($this->Field, $Param, TAGIHAN);
			$InsertResult = mysql_query($InsertQuery) or die(mysql_error());
			
			$Result['tagihan_id'] = mysql_insert_id();
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data berhasil tersimpan.';
			
			$this->M_Log->Write('Menambah Tagihan (' . $Result['tagihan_id'] . ')', $InsertQuery);
		} else {
			$UpdateQuery  = GenerateUpdateQuery($this->Field, $Param, TAGIHAN);
			$UpdateResult = mysql_query($UpdateQuery) or die(mysql_error());
			
			$Result['tagihan_id'] = $Param['tagihan_id'];
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data berhasil diperbaharui.';
			
			$this->M_Log->Write('Mengubah Tagihan (' . $Result['tagihan_id'] . ')', $UpdateQuery);
		}
		
		return $Result;
	}
	
	function GetByID($Param) {
		$Array = array();
		
		if (isset($Param['tagihan_id'])) {
			$SelectQuery  = "
				SELECT Tagihan.*
				FROM ".TAGIHAN." Tagihan
				WHERE Tagihan.id = '".$Param['tagihan_id']."' LIMIT 1";
		}
		
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		if (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$Array = StripArray($Row);
		}
		
		return $Array;
	}
	
	function GetNilai($Param) {
		$Result['tagihan_nilai'] = $Param['tagihan_nilai'];
		
		if ($Param['tagihan_type_id'] == IURAN_BULANAN_ID) {
			$Result['tagihan_nilai'] = (in_array($Param['hubungankeluarga'], array('03n', '05n'))) ? $Param['IuranAnak']['config'] : $Param['IuranDewasa']['config'];
		}
		
		return $Result;
	}
	
	function GetArray($Param = array()) {
		$Array = array();
		$StringSearch = (isset($Param['NameLike'])) ? "AND Jemaat.nama LIKE '" . $Param['NameLike'] . "%'"  : '';
		$StringGereja = (isset($Param['gereja_id']) && !empty($Param['gereja_id'])) ? "AND Gereja.id = '" . $Param['gereja_id'] . "'"  : '';
		$StringJemaat = (isset($Param['jemaat_id']) && !empty($Param['jemaat_id'])) ? "AND jemaat_id = '" . $Param['jemaat_id'] . "'"  : '';
		$StringTagihanType = (isset($Param['tagihan_type_id']) && !empty($Param['tagihan_type_id'])) ? "AND Tagihan.tagihan_type_id = '" . $Param['tagihan_type_id'] . "'"  : '';
		$StringDiffNilai = (isset($Param['diff_nilai'])) ? "AND tagihan_nilai <> tagihan_bayar"  : '';
		$StringFilter = GetStringFilter($Param, array('jemaat_nama' => 'Jemaat.nama'));
		
		$PageOffset = (isset($Param['start']) && !empty($Param['start'])) ? $Param['start'] : 0;
		$PageLimit = (isset($Param['limit']) && !empty($Param['limit'])) ? $Param['limit'] : 25;
		$StringSorting = (isset($Param['sort'])) ? GetStringSorting($Param['sort'], array('jemaat_nama' => 'Jemaat.nama')) : 'Jemaat.nama ASC';
		
		$SelectQuery = "
			SELECT Tagihan.*, TagihanType.tagihan_type, Jemaat.nama jemaat_nama
			FROM ".TAGIHAN." Tagihan
			LEFT JOIN ".TAGIHAN_TYPE." TagihanType ON TagihanType.tagihan_type_id = Tagihan.tagihan_type_id
			LEFT JOIN ".JEMAAT." Jemaat ON Jemaat.id = Tagihan.jemaat_id
			LEFT JOIN ".GEREJA." Gereja ON Gereja.id = Jemaat.idgereja
			WHERE 1 $StringSearch $StringGereja $StringJemaat $StringTagihanType $StringDiffNilai $StringFilter
			ORDER BY $StringSorting
			LIMIT $PageOffset, $PageLimit
		";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$Row['tagihan_tanggal'] = ($Row['tagihan_tanggal'] == '0000-00-00') ? null : $Row['tagihan_tanggal'];
			
			$Row = StripArray($Row);
			$Array[] = $Row;
		}
		
		return $Array;
	}
	
	function GetCount($Param = array()) {
		$TotalRecord = 0;
		
		$StringSearch = (isset($Param['NameLike'])) ? "AND Jemaat.nama LIKE '" . $Param['NameLike'] . "%'"  : '';
		$StringGereja = (isset($Param['gereja_id']) && !empty($Param['gereja_id'])) ? "AND Gereja.id = '" . $Param['gereja_id'] . "'"  : '';
		$StringJemaat = (isset($Param['jemaat_id']) && !empty($Param['jemaat_id'])) ? "AND jemaat_id = '" . $Param['jemaat_id'] . "'"  : '';
		$StringTagihanType = (isset($Param['tagihan_type_id']) && !empty($Param['tagihan_type_id'])) ? "AND Tagihan.tagihan_type_id = '" . $Param['tagihan_type_id'] . "'"  : '';
		$StringDiffNilai = (isset($Param['diff_nilai'])) ? "AND tagihan_nilai <> tagihan_bayar"  : '';
		$StringFilter = GetStringFilter($Param, array('jemaat_nama' => 'Jemaat.nama'));
		
		$SelectQuery = "
			SELECT COUNT(*) AS TotalRecord
			FROM ".TAGIHAN." Tagihan
			LEFT JOIN ".TAGIHAN_TYPE." TagihanType ON TagihanType.tagihan_type_id = Tagihan.tagihan_type_id
			LEFT JOIN ".JEMAAT." Jemaat ON Jemaat.id = Tagihan.jemaat_id
			LEFT JOIN ".GEREJA." Gereja ON Gereja.id = Jemaat.idgereja
			WHERE 1 $StringSearch $StringGereja $StringJemaat $StringTagihanType $StringDiffNilai $StringFilter
		";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$TotalRecord = $Row['TotalRecord'];
		}
		
		return $TotalRecord;
	}
	
	function GetArrayGroup($Param = array()) {
		$Array = array();
		$StringSearch = (isset($Param['NameLike'])) ? "AND Jemaat.nama LIKE '" . $Param['NameLike'] . "%'"  : '';
		$StringGereja = (isset($Param['gereja_id']) && !empty($Param['gereja_id'])) ? "AND Gereja.id = '" . $Param['gereja_id'] . "'"  : '';
		$StringFilter = GetStringFilter($Param, array('jemaat_nama' => 'Jemaat.nama'));
		
		$PageOffset = (isset($Param['start']) && !empty($Param['start'])) ? $Param['start'] : 0;
		$PageLimit = (isset($Param['limit']) && !empty($Param['limit'])) ? $Param['limit'] : 25;
		$StringSorting = (isset($Param['sort'])) ? GetStringSorting($Param['sort'], array('jemaat_nama' => 'Jemaat.nama')) : 'Jemaat.nama ASC';
		
		$SelectQuery = "
			SELECT
				jemaat_id, Tagihan.tagihan_type_id, TagihanType.tagihan_type, Jemaat.nama jemaat_nama,
				SUM(tagihan_nilai) tagihan_nilai, SUM(tagihan_bayar) tagihan_bayar, COUNT(*) record_count
			FROM ".TAGIHAN." Tagihan
			LEFT JOIN ".TAGIHAN_TYPE." TagihanType ON TagihanType.tagihan_type_id = Tagihan.tagihan_type_id
			LEFT JOIN ".JEMAAT." Jemaat ON Jemaat.id = Tagihan.jemaat_id
			LEFT JOIN ".GEREJA." Gereja ON Gereja.id = Jemaat.idgereja
			WHERE tagihan_nilai != tagihan_bayar $StringSearch $StringGereja $StringFilter
			GROUP BY jemaat_id, TagihanType.tagihan_type, Jemaat.nama
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
	
	function GetCountGroup($Param = array()) {
		$TotalRecord = 0;
		
		$StringSearch = (isset($Param['NameLike'])) ? "AND Jemaat.nama LIKE '" . $Param['NameLike'] . "%'"  : '';
		$StringGereja = (isset($Param['gereja_id']) && !empty($Param['gereja_id'])) ? "AND Gereja.id = '" . $Param['gereja_id'] . "'"  : '';
		$StringFilter = GetStringFilter($Param, array('jemaat_nama' => 'Jemaat.nama'));
		
		$SelectQuery = "
			SELECT jemaat_id, Tagihan.tagihan_type_id
			FROM ".TAGIHAN." Tagihan
			LEFT JOIN ".TAGIHAN_TYPE." TagihanType ON TagihanType.tagihan_type_id = Tagihan.tagihan_type_id
			LEFT JOIN ".JEMAAT." Jemaat ON Jemaat.id = Tagihan.jemaat_id
			LEFT JOIN ".GEREJA." Gereja ON Gereja.id = Jemaat.idgereja
			WHERE tagihan_nilai != tagihan_bayar $StringSearch $StringGereja $StringFilter
			GROUP BY jemaat_id, tagihan_type_id
		";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		$TotalRecord = mysql_num_rows($SelectResult);
		
		return $TotalRecord;
	}
	
	function Delete($Param) {
		$DeleteQuery  = "DELETE FROM ".TAGIHAN." WHERE tagihan_id = '".$Param['tagihan_id']."' LIMIT 1";
		$DeleteResult = mysql_query($DeleteQuery) or die(mysql_error());
		
		$Result['QueryStatus'] = '1';
		$Result['Message'] = 'Data berhasil dihapus.';
		
		$this->M_Log->Write('Menghapus Tagihan (' . $Param['tagihan_id'] . ')', $DeleteQuery);
		
		return $Result;
	}
}