<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class M_User extends CI_Model {
	function __construct() {
        parent::__construct();
    }
	
	function LoginRequired() {
		$UserAdmin = $this->session->userdata('UserAdmin');
		if (! is_array($UserAdmin) || count($UserAdmin) <= 0) {
			header("Location: " . $this->config->item('base_url') . "/administrator");
			exit;
		}
	}
	
	function GetCurrentUser() {
		$UserAdmin = $this->session->userdata('UserAdmin');
		return $UserAdmin;
	}
	
	function GetGerejaID() {
		$User = $this->GetCurrentUser();
		$gereja_id = $this->M_Permission->GetAccessGerejaID($User);
		return $gereja_id;
	}
	
	function Update($User) {
		$Result = array();
		$User = EscapeString($User);
		
		if (empty($User['UserID'])) {
			$InsertQuery  = "
				INSERT INTO ".USER." ( id, username, email, name )
				VALUES ( NULL, '".$User['username']."', '".$User['email']."', '".$User['name']."' )";
			$InsertResult = mysql_query($InsertQuery) or die(mysql_error());
			
			$Result['UserID'] = mysql_insert_id();
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data berhasil tersimpan.';
		} else {
			$UpdateQuery  = "
				UPDATE ".USER."
				SET username = '".$User['username']."', email = '".$User['email']."', name = '".$User['name']."'
				WHERE id = '".$User['UserID']."'
				LIMIT 1
			";
			$UpdateResult = mysql_query($UpdateQuery) or die(mysql_error());
			
			$Result['UserID'] = $User['UserID'];
			$Result['QueryStatus'] = '1';
			$Result['Message'] = 'Data berhasil diperbaharui.';
		}
		
		return $Result;
	}
	
	function UpdateLogin($Param) {
		$UpdateQuery  = "UPDATE ".USER." SET last_login = '".$Param['last_login']."' WHERE id = '".$Param['UserID']."' LIMIT 1";
		$UpdateResult = mysql_query($UpdateQuery) or die(mysql_error());
	}
	
	function UpdatePassword($Param) {
		$UpdateQuery  = "UPDATE ".USER." SET password = '".$Param['password']."' WHERE id = '".$Param['UserID']."' LIMIT 1";
		$UpdateResult = mysql_query($UpdateQuery) or die(mysql_error());
	}
    
	function UpdateGroup($Param) {
        $DeleteQuery  = "DELETE FROM ".USER_GROUP." WHERE user_id = '".$Param['UserID']."'";
		$DeleteResult = mysql_query($DeleteQuery) or die(mysql_error());
        
		$InsertQuery  = "INSERT INTO ".USER_GROUP." (user_id, group_id) VALUES ('".$Param['UserID']."', '".$Param['group_id']."')";
		$InsertResult = mysql_query($InsertQuery) or die(mysql_error());
	}
	
	function UpdateFoto($Param) {
		$UpdateQuery  = "
			UPDATE ".USER." SET foto = '".$Param['foto']."'
			WHERE id = '".$Param['UserID']."'
			LIMIT 1
		";
		$UpdateResult = mysql_query($UpdateQuery) or die(mysql_error());
		
		$Result['QueryStatus'] = '1';
		$Result['Message'] = 'Data berhasil diperbaharui.';
		
		return $Result;
	}
	
	function UpdateResetPassword($Param) {
		$UpdateQuery  = "UPDATE ".USER." SET reset = '".$Param['reset']."' WHERE id = '".$Param['UserID']."' LIMIT 1";
		$UpdateResult = mysql_query($UpdateQuery) or die(mysql_error());
	}
	
	function GetByID($User) {
		$User = EscapeString($User);
		
		$Array = array();
        
		if (isset($User['username'])) {
            $SelectQuery  = "
				SELECT User.*, UserGroup.group_id, UserGereja.gereja_id
				FROM ".USER." User
				LEFT JOIN ".USER_GROUP." UserGroup ON User.id = UserGroup.user_id
				LEFT JOIN ".USER_GEREJA." UserGereja ON User.id = UserGereja.user_id
				WHERE username = '".$User['username']."'
				LIMIT 1";
		} else if (isset($User['email'])) {
            $SelectQuery  = "SELECT * FROM ".USER." WHERE email = '".$User['email']."' LIMIT 1";
		} else if (isset($User['reset'])) {
            $SelectQuery  = "SELECT * FROM ".USER." WHERE reset = '".$User['reset']."' LIMIT 1";
		} else if (isset($User['UserID'])) {
            $SelectQuery  = "
                SELECT User.*, UserGroup.group_id
                FROM ".USER." User
                LEFT JOIN ".USER_GROUP." UserGroup ON User.id = UserGroup.user_id
                WHERE User.id = '".$User['UserID']."'
                LIMIT 1";
        }
        
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		if (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$Array = StripArray($Row);
            $Array['UserID'] = $Array['id'];
			
			$FileFoto = $this->config->item('base_path') . '/images/user/' . $Array['foto'];
			if (!empty($Array['foto']) && file_exists($FileFoto)) {
				$Array['FotoLink'] = $this->config->item('base_url') . '/images/user/' . $Array['foto'];
			} else {
				$Array['FotoLink'] = $this->config->item('base_url') . '/images/default-image.png';
			}
		}
		
		return $Array;
	}
	
	function GetArrayMenu($Param) {
		$ArrayResultMenu = array();
		$Permission = $this->M_Permission->GetCollection(array('GroupID' => $Param['GroupID']));
		foreach ($Permission['PermissionData'] as $Key => $Array) {
			if ($Array['Read'] == 1) {
				$ArrayResultMenu[] = $Array;
			}
		}
		
		return $ArrayResultMenu;
	}
	
	function GetArrayMenuGroup($Param) {
		$Array = $this->GetArrayMenu($Param);
		
		$ArrayTemp = array();
		foreach ($Array as $Key => $Temp) {
			$ArrayTemp[$Temp['Group']][] = $Temp;
		}
		
		$Counter = 0;
		$ArrayResult = array();
		foreach ($ArrayTemp as $Key => $Temp) {
			$ArrayResult[$Counter]['Title'] = $Key;
			$ArrayResult[$Counter]['Child'] = $Temp;
			
			$Counter++;
		}
		
		return $ArrayResult;
	}
	
	function GetArray($Param = array()) {
		$Array = array();
		$StringSearch = (isset($Param['NameLike'])) ? "AND name LIKE '" . $Param['NameLike'] . "%'"  : '';
		$StringUserID = (isset($Param['UserID']) && !empty($Param['UserID'])) ? "AND id = '" . $Param['UserID'] . "'"  : '';
		$StringFreeAdminGereja = (isset($Param['OnlyFreeAdminGereja']) && !empty($Param['OnlyFreeAdminGereja'])) ? "AND UserGereja.gereja_id IS NULL"  : '';
		$StringFilter = GetStringFilter($Param, array('group_name' => '_Group.group_name'));
		
		$PageOffset = (isset($Param['start']) && !empty($Param['start'])) ? $Param['start'] : 0;
		$PageLimit = (isset($Param['limit']) && !empty($Param['limit'])) ? $Param['limit'] : 25;
		
		$SortingTemp = (isset($Param['sort']) && !empty($Param['sort'])) ? json_decode($Param['sort']) : 'username';
		$Sorting = (is_array($SortingTemp)) ? $SortingTemp[0]->property : $SortingTemp;
		$Ordering = (is_array($SortingTemp)) ? $SortingTemp[0]->direction : 'ASC';
		
		$SelectQuery = "
			SELECT User.*, _Group.group_name
			FROM ".USER." User
            LEFT JOIN ".USER_GROUP." UserGroup ON User.id = UserGroup.user_id
            LEFT JOIN ".GROUP." _Group ON _Group.group_id = UserGroup.group_id
			LEFT JOIN ".USER_GEREJA." UserGereja ON User.id = UserGereja.user_id
			WHERE 1 $StringSearch $StringUserID $StringFreeAdminGereja $StringFilter
			ORDER BY $Sorting $Ordering
			LIMIT $PageOffset, $PageLimit
		";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
            unset($Row['password']);
            
			$Row = StripArray($Row);
			$Array[] = $Row;
		}
		
		return $Array;
	}
	
	function GetCount($Param = array()) {
		$TotalRecord = 0;
		
		$StringSearch = (isset($Param['NameLike'])) ? "AND name LIKE '" . $Param['NameLike'] . "%'"  : '';
		$StringUserID = (isset($Param['UserID']) && !empty($Param['UserID'])) ? "AND id = '" . $Param['UserID'] . "'"  : '';
		$StringFilter = GetStringFilter($Param, array('group_name' => '_Group.group_name'));
		
		$SelectQuery = "
			SELECT COUNT(*) AS TotalRecord
			FROM ".USER." User
            LEFT JOIN ".USER_GROUP." UserGroup ON User.id = UserGroup.user_id
            LEFT JOIN ".GROUP." _Group ON _Group.group_id = UserGroup.group_id
			WHERE 1 $StringSearch $StringUserID $StringFilter
		";
		$SelectResult = mysql_query($SelectQuery) or die(mysql_error());
		while (false !== $Row = mysql_fetch_assoc($SelectResult)) {
			$TotalRecord = $Row['TotalRecord'];
		}
		
		return $TotalRecord;
	}
	
	function Delete($Param) {
        $DeleteQuery  = "DELETE FROM ".USER." WHERE id = '".$Param['UserID']."' LIMIT 1";
        $DeleteResult = mysql_query($DeleteQuery) or die(mysql_error());
        $DeleteQuery  = "DELETE FROM ".USER_GROUP." WHERE user_id = '".$Param['UserID']."' LIMIT 1";
        $DeleteResult = mysql_query($DeleteQuery) or die(mysql_error());
        
        $Result['QueryStatus'] = '1';
        $Result['Message'] = 'Data berhasil dihapus.';
		
		return $Result;
	}
	
	function RequestPassword($Param) {
		$User = $this->GetByID(array('email' => $Param['Email']));
		if (count($User) > 0) {
			$TempValue = date("Y-m-d H:i:s") . rand(1000,9999);
			$ResetValue = md5($TempValue);
			$this->UpdateResetPassword(array('UserID' => $User['UserID'], 'reset' => $ResetValue));
			
			$Message  = "Seseorang telah melakukan reset password untuk account :\n";
			$Message .= "Username : ".$User['username']."\n";
			$Message .= "Email : ".$User['email']."\n";
			$Message .= "Jika ini adalah kesalahan, maka abaikan email ini.\n";
			$Message .= "Untuk melakukan reset password, silahkan klik pada link berikut :\n";
			$Message .= $this->config->item('base_url') . '/administrator/action/reset-password/' . $ResetValue;
			@mail($Param['Email'], 'Reset Password', $Message);
			
			$Result['QueryStatus'] = 1;
			$Result['Message'] = 'Reset password berhasil dikirimkan ke email anda.';
		} else {
			$Result['QueryStatus'] = 0;
			$Result['Message'] = 'Maaf, email anda tidak ditemukan.';
		}
		
		return $Result;
	}
	
	function ResetPassword($ResetValue) {
		$User = $this->GetByID(array('reset' => $ResetValue));
		if (count($User) > 0 && !empty($ResetValue)) {
			$TempValue = date("Y-m-d H:i:s") . rand(1000,9999);
			$Password = substr(md5($TempValue), 0, 20);
			
			$Message  = "Password account anda berhasil direset, berikut informasi account anda :\n\n";
			$Message .= "Username : ".$User['username']."\n";
			$Message .= "Email : ".$User['email']."\n";
			$Message .= "Password : ".$Password."\n\n";
			$Message .= "Terima Kasih\n";
			$Message .= "Admin";
			@mail($User['email'], 'Informasi Password Baru', $Message);
			
			$this->UpdateResetPassword(array('UserID' => $User['UserID'], 'reset' => ''));
			$this->UpdatePassword(array('UserID' => $User['UserID'], 'password' => md5($Password)));
			
			$Result = 'Silahkan memeriksa email anda untuk informasi password baru.';
		} else {
			$Result = 'Maaf, link ini sudah tidak aktif.';
		}
		return $Result;
	}
}