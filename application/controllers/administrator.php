<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class administrator extends CI_Controller {

	function home() {
		$this->load->view('administrator/home');
	}
	
	function getmenu() {
		$this->load->view('administrator/content_menu_left');
	}
	
	function welcome() {
		$this->load->view('administrator/welcome');
	}
    
    function getheader() {
		$this->load->view('administrator/headerp');
    }
	
	function GetFooter() {
        $this->M_User->LoginRequired();
		$UserAdmin = $this->session->userdata('UserAdmin');
		$AccessGerejaID = $this->M_Permission->GetAccessGerejaID($UserAdmin);
		
		$SiteFooter = $this->M_Config->GetByID(array('config_name' => 'Site Footer', 'gereja_id' => $AccessGerejaID));
		echo (isset($SiteFooter['config'])) ? $SiteFooter['config'] : '@ ' . date("Y") . ' PT Sinar Media 3';
	}
	
	function check() {
		//cek apakah user login atau ga, kalau login, berikan menu dia, dalam format HTML
		if ( $UserAdmin = $this->session->userdata('UserAdmin') ) {
			$ArrayMenu = $this->M_User->GetArrayMenuGroup(array('GroupID' => $UserAdmin['group_id']));
			
			echo json_encode( array( 'success' => true, 'menu' => $ArrayMenu ));
			return;
		}
        
		show_error("Not logged in", 403);
	}
    
	function index() {
		$Data['Message'] = '';
		if (isset($_POST['username'])) {
			$Admin = $this->M_User->GetByID(array('username' => $_POST['username'], 'WithFoto' => 1));
			
			if (count($Admin) == 0) {
				$Data['Message'] = 'Maaf, user tidak ditemukan';
			} else {
				$password = (isset($_POST['password'])) ? $_POST['password'] : '';
				if ($Admin['password'] == md5(sha1(SHA_SECRET . ':' . $password))) {
					$this->session->set_userdata(array('UserAdmin' => $Admin));
					
					// Update Last Login
					$ParamAdmin = $Admin;
					$ParamAdmin['last_login'] = $this->config->item('current_time');
					$this->M_User->UpdateLogin($ParamAdmin);
					
					// X-Requested-With
					if ( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ) {
						unset($Admin['AdminPassWord']);
						$ArrayMenu = $this->M_User->GetArrayMenuGroup(array('GroupID' => $Admin['group_id']));
						echo json_encode( array( 'success' => true, 'menu' => $ArrayMenu, 'UserAdmin' => $Admin ));
						return;
					}
					
					$LinkRedirect = $this->M_User->GetAdminRedirectPage($Admin);
					header("Location: " . $LinkRedirect); exit;
				}
			}
		}
                
		if ( isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' ) {
			echo json_encode(array('success'=>false,'text'=>$Data['Message']));
			return;
		}
		
		$this->load->view('administrator/home');
	}
	
	function grid() {
		$RequestName = (isset($_GET['RequestName'])) ? $_GET['RequestName'] : '';
		$SetSession = (isset($_GET['SetSession'])) ? $_GET['SetSession'] : 0;
		
		if ($SetSession == 1) {
			$SessionName = date("Ymd_His") . '_' . rand(1000,9999);
			
			$Array['SessionName'] = $SessionName;
			$this->session->set_userdata(array($SessionName => $_GET));
		}
		
		if ($RequestName == 'Keluarga') {
			$Array['KeluargaData'] = $this->M_Keluarga->GetArray($_GET);
			$Array['KeluargaCount'] = $this->M_Keluarga->GetCount($_GET);
		} else if ($RequestName == 'Jemaat') {
			$Array['JemaatData'] = $this->M_Jemaat->GetArray($_GET);
			$Array['JemaatCount'] = $this->M_Jemaat->GetCount($_GET);
		} else if ($RequestName == 'Gereja') {
			$Array['GerejaData'] = $this->M_Gereja->GetArray($_GET);
			$Array['GerejaCount'] = $this->M_Gereja->GetCount($_GET);
		} else if ($RequestName == 'User') {
			$Array['UserData'] = $this->M_User->GetArray($_GET);
			$Array['UserCount'] = $this->M_User->GetCount($_GET);
		} else if ($RequestName == 'Group') {
			$Array['GroupData'] = $this->M_Group->GetArray($_GET);
			$Array['GroupCount'] = $this->M_Group->GetCount($_GET);
		} else if ($RequestName == 'Profesi') {
			$Array['ProfesiData'] = $this->M_Profesi->GetArray($_GET);
			$Array['ProfesiCount'] = $this->M_Profesi->GetCount($_GET);
		} else if ($RequestName == 'Pendidikan') {
			$Array['PendidikanData'] = $this->M_Pendidikan->GetArray($_GET);
			$Array['PendidikanCount'] = $this->M_Pendidikan->GetCount($_GET);
		} else if ($RequestName == 'Permission') {
			$Array = $this->M_Permission->GetCollection($_GET);
		} else {
			$Array['AdmissionData'] = $this->M_Pekerjaan->GetArray($_GET);
			$Array['AdmissionCount'] = 10;
		}
		
		echo json_encode($Array); exit;
	}
	
	function request($PageName) {
		$this->load->view('administrator/'.$PageName.'.php');
	}
	
	function combo() {
		$Action = (isset($_GET['Action'])) ? $_GET['Action'] : '';
		$Action = (isset($_POST['Action'])) ? $_POST['Action'] : $Action;
		$NameLike = (isset($_GET['query'])) ? $_GET['query'] : '';
		$NameLike = (isset($_POST['query'])) ? $_POST['query'] : $NameLike;
		$ForceDisplayID = (isset($_POST['ForceDisplayID'])) ? $_POST['ForceDisplayID'] : 0;
		
		$User = $this->M_User->GetCurrentUser();
		
		if ($Action == 'AdminGereja') {
			$Result = $this->M_User->GetArray(array('OnlyFreeAdminGereja' => 1, 'filter' => '[{"type":"numeric","comparison":"eq","value":"4","field":"UserGroup.group_id"}]'));
		} else if ($Action == 'Gereja') {
			$Param = array(
				'NameLike' => $NameLike,
				'id' => (empty($User['gereja_id'])) ? 0 : $User['gereja_id']
			);
			if (!empty($ForceDisplayID)) {
				$Param['ForceDisplayID'] = $ForceDisplayID;
			}
			$Result = $this->M_Gereja->GetArray($Param);
		} else if ($Action == 'GolonganDarah') {
			$Result = $this->M_Golongan_Darah->GetArray(array());
		} else if ($Action == 'Group') {
			$Result = $this->M_Group->GetArray();
		} else if ($Action == 'HubunganKeluarga') {
			$Result = $this->M_Hubungan_Keluarga->GetArray(array());
		} else if ($Action == 'Jemaat') {
			$Param = array(
				'NameLike' => $NameLike,
				'idgereja' => (empty($User['gereja_id'])) ? 0 : $User['gereja_id']
			);
			
			$_POST['gereja_id'] = (isset($_POST['gereja_id'])) ? $_POST['gereja_id'] : 0;
			if (!empty($_POST['gereja_id'])) {
				$Param['idgereja'] = $_POST['gereja_id'];
			}
			
			if (!empty($ForceDisplayID)) {
				$Param['ForceDisplayID'] = $ForceDisplayID;
			}
			$Result = $this->M_Jemaat->GetArray($Param);
		} else if ($Action == 'JenisBiaya') {
			$Result = $this->M_Jenis_Biaya->GetArray(array());
		} else if ($Action == 'Kelurahan') {
			$Result = $this->M_Kelurahan->GetArray(array('NameLike' => $NameLike));
		} else if ($Action == 'Kecamatan') {
			$Result = $this->M_Kecamatan->GetArray(array('NameLike' => $NameLike));
		} else if ($Action == 'Kota') {
			$idpropinsi = (isset($_POST['idpropinsi'])) ? $_POST['idpropinsi'] : 0;
			$Result = $this->M_Kota->GetArray(array('NameLike' => $NameLike, 'idpropinsi' => $idpropinsi));
		} else if ($Action == 'Keluarga') {
			$idgereja = (isset($_POST['idgereja'])) ? $_POST['idgereja'] : 0;
			$idkeluarga = (isset($_POST['idkeluarga'])) ? $_POST['idkeluarga'] : 0;
			$Result = $this->M_Keluarga->GetArray(array('NameLike' => $NameLike, 'idgereja' => $idgereja, 'idkeluarga' => $idkeluarga));
		} else if ($Action == 'MetodeKirim') {
			$Result = $this->M_Metode_Kirim->GetArray(array());
		} else if ($Action == 'Negara') {
			$Result = $this->M_Negara->GetArray(array('NameLike' => $NameLike));
		} else if ($Action == 'Profesi') {
			$Result = $this->M_Profesi->GetArray(array('NameLike' => $NameLike));
		} else if ($Action == 'Pendidikan') {
			$Result = $this->M_Pendidikan->GetArray(array('NameLike' => $NameLike));
		} else if ($Action == 'Propinsi') {
			$idnegara = (isset($_POST['idnegara'])) ? $_POST['idnegara'] : 0;
			$Result = $this->M_Propinsi->GetArray(array('NameLike' => $NameLike, 'idnegara' => $idnegara));
		} else if ($Action == 'Sex') {
			$Result = $this->M_Sex->GetArray(array());
		} else if ($Action == 'Status') {
			$Result = $this->M_Status->GetArray(array());
		} else if ($Action == 'TagihanType') {
			$Result = $this->M_Tagihan_Type->GetArray(array());
		}
		
		echo json_encode($Result);
	}
	
	function ajax() {
		$Result = array();
		$Action = (isset($_POST['Action'])) ? $_POST['Action'] : '';
		$UserAdmin = $this->session->userdata('UserAdmin');
		
		// Jemaat
		if ($Action == 'GetJemaatByID') {
			$Result = $this->M_Jemaat->GetByID(array('JemaatID' => $_POST['JemaatID']));
		} else if ($Action == 'GerNomorJemaat') {
			$Result = $this->M_Jemaat->GetNextNomor(array('Nomor' => $_POST['Nomor']));
		} else if ($Action == 'EditJemaat') {
			$Param = $_POST;
			$Param['InsertBy'] = $UserAdmin['username'];
			$Param['UpdateBy'] = $UserAdmin['username'];
			$Param['InsertTime'] = $this->config->item('current_time');
			$Param['UpdateTime'] = $this->config->item('current_time');
            
            $Jemaat = $this->M_Jemaat->GetByID(array('nomor' => $Param['nomor']));
            if (count($Jemaat) > 0 && $Jemaat['JemaatID'] != $Param['JemaatID']) {
                $Result['QueryStatus'] = '0';
                $Result['Message'] = 'Maaf, nomor sudah terpakai.'.$Param['nomor'];
            } else {
                $Result = $this->M_Jemaat->Update($Param);
				
				if (isset($Param['foto']) && !empty($Param['foto']) && $Param['JemaatID'] == 0) {
					$ParamFoto['foto'] = $Param['foto'];
					$ParamFoto['JemaatID'] = $Result['JemaatID'];
					$this->M_Jemaat->UpdateFoto($ParamFoto);
				}
            }
		} else if ($Action == 'DeteleJemaatByID') {
			$Result = $this->M_Jemaat->Delete(array('JemaatID' => $_POST['JemaatID']));
		}
		
		// Keluarga
        else if ($Action == 'GetKeluargaByID') {
			$Result = $this->M_Keluarga->GetByID(array('KeluargaID' => $_POST['KeluargaID']));
		} else if ($Action == 'EditKeluarga') {
			$Param = $_POST;
			$Param['InsertBy'] = $UserAdmin['username'];
			$Param['UpdateBy'] = $UserAdmin['username'];
			$Param['InsertTime'] = $this->config->item('current_time');
			$Param['UpdateTime'] = $this->config->item('current_time');
			
			$Keluarga = $this->M_Keluarga->GetByID(array('nomor' => $Param['nomor']));
            if (count($Keluarga) > 0 && $Keluarga['KeluargaID'] != $Param['KeluargaID']) {
                $Result['QueryStatus'] = '0';
                $Result['Message'] = 'Maaf, nomor sudah terpakai.';
            } else {
				$Result = $this->M_Keluarga->Update($Param);
			}
		} else if ($Action == 'DeteleKeluargaByID') {
			$Result = $this->M_Keluarga->Delete(array('KeluargaID' => $_POST['KeluargaID']));
		}
		
		// Gereja
		else if ($Action == 'GetGerejaByID') {
			$Result = $this->M_Gereja->GetByID(array('GerejaID' => $_POST['GerejaID']));
		} else if ($Action == 'EditGereja') {
			$Param = $_POST;
			$Param['InsertBy'] = $UserAdmin['username'];
			$Param['UpdateBy'] = $UserAdmin['username'];
			$Param['InsertTime'] = $this->config->item('current_time');
			$Param['UpdateTime'] = $this->config->item('current_time');
			$Result = $this->M_Gereja->Update($Param);
		} else if ($Action == 'DeteleGerejaByID') {
			$Result = $this->M_Gereja->Delete(array('GerejaID' => $_POST['GerejaID']));
		}
		
		// User
		else if ($Action == 'GetUserByID') {
			$Result = $this->M_User->GetByID(array('UserID' => $_POST['UserID']));
		} else if ($Action == 'EditUser') {
			$Param = $_POST;
            $UserByUserName = $this->M_User->GetByID(array('username' => $Param['username']));
            $UserByEmail = $this->M_User->GetByID(array('email' => $Param['email']));
            if (count($UserByUserName) > 0 && $UserByUserName['UserID'] != $Param['UserID']) {
                $Result['QueryStatus'] = '0';
                $Result['Message'] = 'Maaf, username sudah terpakai.';
            } else if (count($UserByEmail) > 0 && $UserByEmail['UserID'] != $Param['UserID']) {
                $Result['QueryStatus'] = '0';
                $Result['Message'] = 'Maaf, email sudah terpakai.';
            } else {
                $Result = $this->M_User->Update($Param);
                if (!empty($Param['password'])) {
                    $this->M_User->UpdatePassword(array('UserID' => $Result['UserID'], 'password' => md5(sha1(SHA_SECRET . ':' . $Param['password']))));
                }
                if (!empty($Param['group_id'])) {
                    $this->M_User->UpdateGroup(array('UserID' => $Result['UserID'], 'group_id' => $Param['group_id']));
                }
            }
		} else if ($Action == 'DeteleUserByID') {
			$Result = $this->M_User->Delete(array('UserID' => $_POST['UserID']));
		} else if ($Action == 'RequestResetPassword') {
			$Result = $this->M_User->RequestPassword(array('Email' => $_POST['email']));
		}
		
		// Profesi
		else if ($Action == 'GetProfesiByID') {
			$Result = $this->M_Profesi->GetByID(array('ProfesiID' => $_POST['ProfesiID']));
		} else if ($Action == 'EditProfesi') {
			$Param = $_POST;
            $Profesi = $this->M_Profesi->GetByID(array('profesi' => $Param['profesi']));
            if (count($Profesi) > 0 && $Profesi['ProfesiID'] != $Param['ProfesiID']) {
                $Result['QueryStatus'] = '0';
                $Result['Message'] = 'Maaf, Profesi sudah ada.';
            } else {
                $Result = $this->M_Profesi->Update($Param);
            }
		} else if ($Action == 'DeteleProfesiByID') {
			$Result = $this->M_Profesi->Delete(array('ProfesiID' => $_POST['ProfesiID']));
		}
		
		// Pendidikan
		else if ($Action == 'GetPendidikanByID') {
			$Result = $this->M_Pendidikan->GetByID(array('PendidikanID' => $_POST['PendidikanID']));
		} else if ($Action == 'EditPendidikan') {
			$Param = $_POST;
            $Pendidikan = $this->M_Pendidikan->GetByID(array('pendidikan' => $Param['pendidikan']));
            if (count($Pendidikan) > 0 && $Pendidikan['PendidikanID'] != $Param['PendidikanID']) {
                $Result['QueryStatus'] = '0';
                $Result['Message'] = 'Maaf, Pendidikan sudah ada.';
            } else {
                $Result = $this->M_Pendidikan->Update($Param);
            }
		} else if ($Action == 'DetelePendidikanByID') {
			$Result = $this->M_Pendidikan->Delete(array('PendidikanID' => $_POST['PendidikanID']));
		}
        
		// Permisson
		else if ($Action == 'UpdatePermission') {
			$Result = $this->M_Permission->Update($_POST);
		}
		
		// Upload Tranfer
		else if ($Action == 'ShowTranferUpload') {
			$Result['Content'] = $this->load->view('jemaat/popup/import_grid', array(), true);
		} else if ($Action == 'SaveTranferUpload') {
			$ArrayRaw = GetArrayFromFileUpload($this->config->item('base_path') . '/images/other/' . $_POST['FileUpload']);
			foreach ($ArrayRaw as $Key => $Array) {
				$nomor = 'E_' . GetNextAutoIncrement(JEMAAT);
				$ParamInsert = array(
					'id' => 0,
					'nomor' => $nomor,
					'nama' => $Array[0],
					'tgllahir' => $Array[1],
					'tempatlahir' => $Array[2],
					'alamat' => $Array[3],
					'telpon' => $Array[4],
					'hp' => $Array[5],
					'email' => $Array[6]
				);
				$Result = $this->M_Jemaat->UpdateCommon($ParamInsert);
			}
		}
		
		echo json_encode($Result);
	}
	
	function upload() {
		$UserAdmin = $this->session->userdata('UserAdmin');
		$FormType = (isset($_POST['FormType'])) ? $_POST['FormType'] : '';
		
		$Upload['Status'] = 0;
		$Upload['Message'] = '';
		$Upload['PhotoLink'] = '';
		
		if ($FormType == 'Gereja') {
			// Update Normal Entry
			$Param = $_POST;
			$Param['InsertBy'] = $UserAdmin['username'];
			$Param['UpdateBy'] = $UserAdmin['username'];
			$Param['InsertTime'] = $this->config->item('current_time');
			$Param['UpdateTime'] = $this->config->item('current_time');
			$ResultEntry = $this->M_Gereja->Update($Param);
			$Upload['Message'] = $ResultEntry['Message'];
			$Upload['Status'] = $ResultEntry['QueryStatus'];
			
			// Update Admin Gereja
			$this->M_Gereja->UpdateAdmin(array('UserID' => $Param['UserID'], 'GerejaID' => $ResultEntry['GerejaID']));
			
			// Update Logo
			$Logo = Upload('Logo', $this->config->item('base_path') . '/images/logo');
			if (!empty($Logo['FileDirName'])) {
				$Param = $ResultEntry;
				$Param['logo'] = $Logo['FileDirName'];
				$ResultLogo = $this->M_Gereja->UpdateLogo($Param);
				
				$Upload['Message'] = $ResultLogo['Message'];
				$Upload['Status'] = $ResultLogo['QueryStatus'];
			}
		}
		else if ($FormType == 'UserPhoto') {
			$Upload['Message'] = 'Foto gagal diupload';
			
			// Update Foto
			$foto = Upload('foto', $this->config->item('base_path') . '/images/user');
			if (!empty($foto['FileDirName'])) {
				$Param['UserID'] = $_POST['UserID'];
				$Param['foto'] = $foto['FileDirName'];
				$ResultFoto = $this->M_User->UpdateFoto($Param);
				
				$Upload['Message'] = $ResultFoto['Message'];
				$Upload['Status'] = $ResultFoto['QueryStatus'];
			}
		}
		else if ($FormType == 'Jemaat') {
			$Upload['Message'] = 'Foto gagal diupload';
			
			// Update Foto
			$foto = Upload('foto', $this->config->item('base_path') . '/images/jemaat');
			if (!empty($foto['FileDirName'])) {
				$Param['JemaatID'] = $_POST['JemaatID'];
				$Param['foto'] = $foto['FileDirName'];
				if (!empty($Param['JemaatID'])) {
					$ResultFoto = $this->M_Jemaat->UpdateFoto($Param);
					$Upload['Message'] = $ResultFoto['Message'];
					$Upload['Status'] = $ResultFoto['QueryStatus'];
				} else {
					$Upload['Message'] = 'Foto berhasil dipersiapkan untuk jemaat ini.';
					$Upload['Status'] = 1;
				}
				
				$Upload['PhotoLink'] = $this->config->item('base_url') . '/images/jemaat/' . $foto['FileDirName'];
			}
		}
		else if ($FormType == 'ImportJemaat') {
			$FileImport = Upload('FileImport', $this->config->item('base_path') . '/images/other', array('AllowedExtention' => array('csv')));
			if (!empty($FileImport['FileDirName'])) {
				$Upload['Status'] = 1;
				$Upload['Message'] = $FileImport['Message'];
				$Upload['PhotoLink'] = $FileImport['FileDirName'];
			}		
		}
		
		echo "{ success: true, UploadStatus: ".$Upload['Status'].", Message: '".$Upload['Message']."', PhotoLink: '".$Upload['PhotoLink']."' }";
		exit;
	}
	
	function entry($EntryType) {
        $this->M_User->LoginRequired();
		
		$PageTitle = array( );
		$PageTitle[$EntryType] = (isset($PageTitle[$EntryType])) ? $PageTitle[$EntryType] : ucwords(str_replace('-', ' ', $EntryType));
		
		$this->load->view('administrator/header', array('PageTitle' => $PageTitle[$EntryType]));
		$this->load->view('administrator/entry-'.$EntryType);
		$this->load->view('administrator/footer');
	}
	
	function action() {
		$Param = func_get_args();
		$Action = (isset($Param[0]) && !empty($Param[0])) ? $Param[0] : '';
		
		if ($Action == 'reset-password') {
			$ResetValue = (isset($Param[1]) && !empty($Param[1])) ? $Param[1] : '';
			$Result = $this->M_User->ResetPassword($ResetValue);
			echo $Result; exit;
		}
	}
	
	function logout() {
		$this->session->unset_userdata('UserAdmin');
		header("Location: " . $this->config->item('base_url') . "/administrator");
	}
	
	function test_jemaat(){
		if ($this->M_Jemaat->update_indocrm_customer('67','insert', 'testx', '08909099', ''))
			echo "OK";
		else
			echo "NOK";
	}
}
