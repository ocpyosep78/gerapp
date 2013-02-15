<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_Jemaat extends CI_Model {
	function __construct() {
        parent::__construct();
		
		$this->Field = array(
			'id', 'nama', 'InsertBy', 'InsertTime', 'nomor', 'idkeluarga', 'tgllahir', 'tempatlahir', 'golongandarah', 'tanggaldaftar', 'alamat', 'rtrw',
			'kelurahan', 'kecamatan', 'kodepos', 'telpon', 'hp', 'catatan', 'status', 'meninggal', 'firstname', 'lastname', 'email', 'sex', 'profesi',
			'institusi', 'jabatan', 'statusbaptis', 'tanggalbaptis', 'statussidi', 'tanggalsidi', 'statusnikah', 'tanggalnikah', 'tempatpemberkatan',
			'pendidikan', 'gelar', 'jurusan', 'hubungankeluarga', 'idgereja', 'kota', 'propinsi', 'negara', 'lengkap', 'UpdateBy', 'UpdateTime',
			'deposit', 'tgl_meninggal', 'tempat_makam', 'tempat_baptis', 'tempat_sidi', 'sektor_id', 'customer_id'
		);
    }
	
	function update_indocrm_customer($clientid, $mode, $nama, $mobile, $email, $mobilelama="", $emaillama="") {
		$conn = @mysql_connect("localhost", "k9071857_indocrm", "sulfat*96");
		@mysql_select_db("k9071857_indocrm", $conn);
		
		if (!$conn){
			return false;
		}else{
			if ($mode == "insert"){
				$sql = "SELECT COUNT(*) as cnt FROM k9071857_indocrm.customers WHERE mobile = '$mobile' AND email = '$email' AND client_id = '$clientid'";
				$res = mysql_query($sql,$conn) or die(mysql_error());
				$row = mysql_fetch_array($res);
				$cnt = $row['cnt'];
			
				if ($cnt>0){
					mysql_close();
					return false;
				}else{
					$sql = "INSERT INTO k9071857_indocrm.customers (first_name, mobile, email, client_id) VALUES ('$nama', '$mobile', '$email', '$clientid')"; 
					mysql_query($sql,$conn) or die(mysql_error());
					mysql_close();
					return true;
				}
			}else if ($mode == "update"){
				$sql = "SELECT COUNT(*) as cnt FROM k9071857_indocrm.customers WHERE mobile = '$mobilelama' AND email = '$emaillama' AND client_id = '$clientid'";
				$res = mysql_query($sql,$conn) or die(mysql_error());
				$row = mysql_fetch_array($res);
				$cnt = $row['cnt'];
				
				if ($cnt>0){
					$sql = "SELECT COUNT(*) as cnt FROM k9071857_indocrm.customers WHERE mobile = '$mobile' AND email = '$email' AND client_id = '$clientid'";
					$res2 = mysql_query($sql,$conn) or die(mysql_error());
					$row2 = mysql_fetch_array($res);
					$cnt2 = $row['cnt'];
					if ($cnt2 >0){
						mysql_close();
						return false;
					}else{
						$sql = "UPDATE k9071857_indocrm.customers SET mobile = '$mobile', email = '$email' WHERE client_id = '$clientid'";
						mysql_query($sql,$conn) or die(mysql_error());
						mysql_close();
						return true;
					}
				}else{
					$sql = "SELECT COUNT(*) as cnt FROM k9071857_indocrm.customers WHERE mobile = '$mobile' AND email = '$email' AND client_id = '$clientid'";
					$res2 = mysql_query($sql,$conn) or die(mysql_error());
					$row2 = mysql_fetch_array($res);
					$cnt2 = $row['cnt'];
					if ($cnt2 >0){
						mysql_close();
						return false;
					}else{
						$sql = "INSERT INTO k9071857_indocrm.customers (first_name, mobile, email, client_id) VALUES ('$nama', '$mobile', '$email', '$clientid')"; 
						mysql_query($sql,$conn) or die(mysql_error());
						mysql_close();
						return true;
					}
				}
			}else if ($mode == "delete"){
				$sql = "DELETE FROM k9071857_indocrm.customers WHERE mobile = '$mobile' AND email = '$email' AND client_id = '$clientid'";
				$rs = mysql_query($sql,$conn);
				if ($rs) {
					mysql_close();
					return true;
				}else{
					mysql_close();
					return false;
				}
			}
		}
	}
	
	function Update($Param) {
		$Param['RequestApi'] = (isset($Param['RequestApi'])) ? $Param['RequestApi'] : 1;
		
		// Add Validation ID
		if (empty($Param['id']) && !empty($Param['JemaatID'])) {
			$Param['id'] = $Param['JemaatID'];
		} else if (empty($Param['JemaatID']) && !empty($Param['id'])) {
			$Param['JemaatID'] = $Param['id'];
		}
		
		$Result = array();
		if (empty($Param['JemaatID'])) {
			$InsertQuery  = GenerateInsertQuery($this->Field, $Param, JEMAAT);
			$InsertResult = mysql_query($InsertQuery) or die(mysql_error());
			
			$Result['JemaatID'] = mysql_insert_id();
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data berhasil tersimpan.';
			
			$this->M_Log->Write('Menambah Jemaat (' . $Result['JemaatID'] . ')', $InsertQuery);
			
			/*
			// Just comment, do not delete
			
			if ($this->update_indocrm_customer('67', 'insert', $Param['nama'], $Param['hp'], $Param['email']))
				$Result['Message'] = 'Data berhasil tersimpan.';
			else
				$Result['Message'] = 'Data berhasil tersimpan. Tapi nomor broadcast gagal disimpan';
			/*	*/
		} else {
			$UpdateQuery  = GenerateUpdateQuery($this->Field, $Param, JEMAAT);
			$UpdateResult = mysql_query($UpdateQuery) or die(mysql_error());
			
			$Result['JemaatID'] = $Param['JemaatID'];
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data berhasil tersimpan.';
			
			$this->M_Log->Write('Mengubah Jemaat (' . $Result['JemaatID'] . ')', $UpdateQuery);
			
			/*
			// Just comment, do not delete
			
			$Param['hiddenhp'] = (isset($Param['hiddenhp'])) ? $Param['hiddenhp'] : '';
			$Param['hiddenemail'] = (isset($Param['hiddenemail'])) ? $Param['hiddenemail'] : '';
			if ($this->update_indocrm_customer('67', 'update', $Param['nama'], $Param['hp'], $Param['email'], $Param['hiddenhp'], $Param['hiddenemail']))
				$Result['Message'] = 'Data berhasil tersimpan.';
			else
				$Result['Message'] = 'Data berhasil tersimpan. Tapi nomor broadcast gagal disimpan';
			/*	*/
				
		}
		
		// Synchronize Customer IndoCrm
		if ($Param['RequestApi'] == 1) {
			$ApiResult = $this->SyncCustomer(array('JemaatID' => $Result['JemaatID']));
			$Result['api_result'] = $ApiResult['ApiStatus'];
		}
		
		return $Result;
	}
	
	function UpdateFoto($Param) {
		$UpdateQuery  = "
			UPDATE ".JEMAAT." SET foto = '".$Param['foto']."'
			WHERE id = '".$Param['JemaatID']."'
			LIMIT 1
		";
		$UpdateResult = mysql_query($UpdateQuery) or die(mysql_error());
		
		$Result['QueryStatus'] = '1';
		$Result['Message'] = 'Foto berhasil diperbaharui.';
		
		$this->M_Log->Write('Mengubah Foto Jemaat (' . $Param['JemaatID'] . ')', $UpdateQuery);
		
		return $Result;
	}
	
	function UpdateCommon($Param) {
		$Result = array();
		
		if (empty($Param['id'])) {
			$InsertQuery  = GenerateInsertQuery($this->Field, $Param, JEMAAT);
			$InsertResult = mysql_query($InsertQuery) or die(mysql_error());
			
			$Result['id'] = mysql_insert_id();
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data successfully stored.';
			
			$this->M_Log->Write('Menambah Jemaat (' . $Result['id'] . ')', $InsertQuery);
		} else {
			$UpdateQuery  = GenerateUpdateQuery($this->Field, $Param, JEMAAT);
			$UpdateResult = mysql_query($UpdateQuery) or die(mysql_error());
			
			$Result['id'] = $Param['id'];
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data successfully updated.';
			
			$this->M_Log->Write('Mengubah Jemaat (' . $Result['id'] . ')', $UpdateQuery);
		}
		
		return $Result;
	}
	
	function AutoUpdateDeposit($Param) {
		$Result = array( 'tagihan_bayar' => 0 );
		$Jemaat = $this->GetByID(array('JemaatID' => $Param['jemaat_id']));
		
		if ($Jemaat['deposit'] > 0) {
			if ($Param['tagihan_nilai'] > $Jemaat['deposit']) {
				$Result['tagihan_bayar'] = $Jemaat['deposit'];
				$JemaatUpdate = array('id' => $Jemaat['id'], 'deposit' => 0);
			} else {
				$Result['tagihan_bayar'] = $Param['tagihan_nilai'];
				$JemaatUpdate = array('id' => $Jemaat['id'], 'deposit' => $Jemaat['deposit'] - $Param['tagihan_nilai']);
			}
			$Update = $this->M_Jemaat->UpdateCommon($JemaatUpdate);
			
			$Result = array_merge($Result, $Update);
		}
		
		return $Result;
	}
	
	function GetByID($Param) {
		$Array = array();
		
		if (isset($Param['JemaatID'])) {
			$SelectQuery  = "
				SELECT Jemaat.*, Keluarga.nama KeluargaNama, Gereja.nama GerejaNama
				FROM ".JEMAAT." Jemaat
				LEFT JOIN ".KELUARGA." Keluarga ON Keluarga.id = Jemaat.idkeluarga
				LEFT JOIN ".GEREJA." Gereja ON Gereja.id = Jemaat.idgereja
				WHERE Jemaat.id = '".$Param['JemaatID']."'
				LIMIT 1
			";
		} else if (isset($Param['nomor'])) {
			$SelectQuery  = "SELECT * FROM ".JEMAAT." WHERE nomor = '".$Param['nomor']."' LIMIT 1";
		} else if (isset($Param['idkeluarga'])) {
			$SelectQuery  = "SELECT * FROM ".JEMAAT." WHERE idkeluarga = '".$Param['idkeluarga']."' ORDER BY hubungankeluarga ASC LIMIT 1";
		}
		
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		if (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$Array = StripArray($Row, array('tgl_meninggal'));
            $Array['JemaatID'] = $Array['id'];
			
			$Array['FotoLink'] = $this->config->item('base_url') . '/images/default-image.png';
			if (!empty($Array['foto'])) {
				$FilePathCheck = $this->config->item('base_path') . '/images/jemaat/' . $Array['foto'];
				$Array['FotoLink'] = (file_exists($FilePathCheck)) ?
					$this->config->item('base_url') . '/images/jemaat/' . $Array['foto'] :
					$this->config->item('base_url') . '/images/default-image.png';
			}
		}
		
		return $Array;
	}
	
	function GetNextNomor($Param) {
		$NomorOrigin = preg_replace('/n$/i', '', $Param['Nomor']);
		$NomorLength = strlen($NomorOrigin);
		
		$NextNumber = 1;
		$SelectQuery  = "SELECT * FROM ".JEMAAT." WHERE LEFT(nomor, $NomorLength) = '$NomorOrigin'";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$Nomor = $Row['nomor'];
			$NomorRecord = str_replace($NomorOrigin, '', $Nomor);
			$NextNumber = $NomorRecord + 1;
		}
		
		$NomorResult = $NomorOrigin . $NextNumber;
		
		return array('Nomor' => $NomorResult);
	}
	
	function GetArray($Param = array()) {
		$Sex = $this->M_Sex->GetArray(array('KeyAsID' => 1));
		
		$Array = array();
		$StringSearch = (isset($Param['NameLike'])) ? "AND Jemaat.nama LIKE '" . $Param['NameLike'] . "%'"  : '';
		$StringGerejaID = (isset($Param['idgereja']) && !empty($Param['idgereja'])) ? "AND idgereja = '" . $Param['idgereja'] . "'"  : '';
		$StringKeluarga = (isset($Param['idkeluarga']) && !empty($Param['idkeluarga'])) ? "AND idkeluarga = '" . $Param['idkeluarga'] . "'"  : '';
		$StringFilter = GetStringFilter($Param, array('GerejaNama' => 'Gereja.nama', 'nama' => 'Jemaat.nama', 'sektor' => 'Sektor.sektor'));
		$StringCustom = (!empty($Param['StringCustom'])) ? $Param['StringCustom'] : '';
		
		$PageOffset = (isset($Param['start']) && !empty($Param['start'])) ? $Param['start'] : 0;
		$PageLimit = (isset($Param['limit']) && !empty($Param['limit'])) ? $Param['limit'] : 25;
		
		$SortingTemp = (isset($Param['sort']) && !empty($Param['sort'])) ? json_decode($Param['sort']) : 'nama';
		$Sorting = (is_array($SortingTemp)) ? $SortingTemp[0]->property : $SortingTemp;
		$Ordering = (is_array($SortingTemp)) ? $SortingTemp[0]->direction : 'ASC';
		
		$Sorting = ($Sorting == 'sex_desc') ? 'sex' : $Sorting;
		
		$SelectQuery = "
			SELECT
				Jemaat.*, CONCAT(MONTH(tgllahir), ' ', DAY(tgllahir)) tgllahir_monthday,
				Gereja.nama GerejaNama, Sektor.sektor
			FROM ".JEMAAT." Jemaat
			LEFT JOIN ".GEREJA." Gereja ON Jemaat.idgereja = Gereja.id
			LEFT JOIN ".SEKTOR." Sektor ON Sektor.sektor_id = Jemaat.sektor_id
			WHERE 1 $StringSearch $StringGerejaID $StringKeluarga $StringFilter $StringCustom
			ORDER BY $Sorting $Ordering
			LIMIT $PageOffset, $PageLimit
		";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$Row = StripArray($Row, array('tgllahir'));
			$Row['sex_desc'] = (isset($Sex[$Row['sex']])) ? $Sex[$Row['sex']]['value'] : '';
			$Row['persekutuan'] = $this->GetPersekutuan(array('tgllahir' => $Row['tgllahir'], 'sex' => $Row['sex']));
			
			$Array[] = $Row;
		}
		
		return $Array;
	}
	
	function GetCount($Param = array()) {
		$TotalRecord = 0;
		
		$StringSearch = (isset($Param['NameLike'])) ? "AND Jemaat.nama LIKE '" . $Param['NameLike'] . "%'"  : '';
		$StringGerejaID = (isset($Param['idgereja']) && !empty($Param['idgereja'])) ? "AND idgereja = '" . $Param['idgereja'] . "'"  : '';
		$StringKeluarga = (isset($Param['idkeluarga']) && !empty($Param['idkeluarga'])) ? "AND idkeluarga = '" . $Param['idkeluarga'] . "'"  : '';
		$StringFilter = GetStringFilter($Param, array('GerejaNama' => 'Gereja.nama', 'nama' => 'Jemaat.nama', 'sektor' => 'Sektor.sektor'));
		
		$SelectQuery = "
			SELECT COUNT(*) AS TotalRecord
			FROM ".JEMAAT." Jemaat
			LEFT JOIN ".GEREJA." Gereja ON Jemaat.idgereja = Gereja.id
			LEFT JOIN ".SEKTOR." Sektor ON Sektor.sektor_id = Jemaat.sektor_id
			WHERE 1 $StringSearch $StringGerejaID $StringKeluarga $StringFilter
		";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$TotalRecord = $Row['TotalRecord'];
		}
		
		return $TotalRecord;
	}
	
	function GetPersekutuan($Param) {
		$DiffDay = DateDiff($Param['tgllahir'], date("Y-m-d"));
		$Old = floor($DiffDay / 365);
		
		if ($Old <= 12) {
			$Persekutuan = 'PA: PERSEKUAN ANAK';
		} else if ($Old <= 17) {
			$Persekutuan = 'PT: PERSEKUTUAN TARUNA';
		} else if ($Old <= 35) {
			$Persekutuan = 'GP: GERAKAN PEMUDA';
		} else if ($Old <= 55 && $Param['sex'] == 'L') {
			$Persekutuan = 'PW: PERSEKUTUAN KAUM BAPAK';
		} else if ($Old <= 55 && $Param['sex'] == 'P') {
			$Persekutuan = 'PW: PERSEKUTUAN WANITA';
		} else if ($Old <= 55 && $Param['sex'] == '') {
			$Persekutuan = '-';
		} else {
			$Persekutuan = 'PKLU: PERSEKUTUAN LANJUT USIA';
		}
		
		return $Persekutuan;
	}
	
	function GetArrayJenisKelamin($Param) {
		$Array = array();
		$SelectQuery  = "SELECT sex, COUNT(*) Total FROM ".JEMAAT." GROUP BY sex";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$Row = StripArray($Row);
			$Row['Total'] = intval($Row['Total']);
			$Array[] = $Row;
		}
		return $Array;
	}
	
	function GetArrayLengkap($Param) {
		$Array = array();
		$ArrayLengkap = array( 0 => 'Data tidak lengkap', 1 => 'Data lengkap' );
		
		$SelectQuery  = "SELECT lengkap, COUNT(*) Total FROM ".JEMAAT." GROUP BY lengkap";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$Row = StripArray($Row);
			$Row['Total'] = intval($Row['Total']);
			$Row['lengkap'] = $ArrayLengkap[$Row['lengkap']];
			$Array[] = $Row;
		}
		return $Array;
	}
	
	function GetArrayProfesi($Param) {
		$Array = array();
		$SelectQuery  = "SELECT UPPER(profesi) profesi, COUNT(*) Total FROM ".JEMAAT." GROUP BY UPPER(profesi)";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$Row = StripArray($Row);
			$Row['Total'] = intval($Row['Total']);
			$Row['profesi'] = (empty($Row['profesi'])) ? 'Belum diketahui' : $Row['profesi'];
			$Array[] = $Row;
		}
		return $Array;
	}
	
	function Delete($Param) {
		$DeleteQuery  = "DELETE FROM ".JEMAAT." WHERE id = '".$Param['JemaatID']."' LIMIT 1";
		$DeleteResult = mysql_query($DeleteQuery) or die(mysql_error());
		
		$Result['QueryStatus'] = '1';
		$Result['Message'] = 'Data berhasil dihapus.';
		
		$this->M_Log->Write('Menghapus Jemaat (' . $Param['JemaatID'] . ')', $DeleteQuery);
		
		return $Result;
	}
	
	// Synchronize Customer IndoCrm
	function SyncCustomer($Param) {
		$Jemaat = $this->GetByID(array( 'JemaatID' => $Param['JemaatID'] ));
		
		// Validation Name
		if (empty($Jemaat['firstname']) && empty($Jemaat['lastname'])) {
			$ArrayName = explode(' ', $Jemaat['nama'], 2);
			$Jemaat['firstname'] = $ArrayName[0];
			$Jemaat['lastname'] = (empty($ArrayName[1])) ? '' : $ArrayName[1];
		}
		
		$ApiParam = array(
			'action' => 'Update',
			'gereja_id' => $Jemaat['idgereja'],
			'customer_id' => $Jemaat['customer_id'],
			'first_name' => $Jemaat['firstname'],
			'last_name' => $Jemaat['lastname'],
			'address' => $Jemaat['alamat'],
			'city' => $Jemaat['kota'],
			'state' => $Jemaat['propinsi'],
			'zip_code' => $Jemaat['kodepos'],
			'phone' => $Jemaat['telpon'],
			'mobile' => $Jemaat['hp'],
			'email' => $Jemaat['email']
		);
		$Result = $this->api->request($this->config->item('indocrm_api') . 'customer', $ApiParam);
		if (!empty($Result['ApiStatus']) && $Result['ApiStatus'] == 1 && empty($Jemaat['customer_id'])) {
			$this->Update(array( 'id' => $Param['JemaatID'], 'customer_id' => $Result['customer_id'], 'RequestApi' => 0 ));
		}
		
		return $Result;
	}
}
