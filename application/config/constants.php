<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('SHA_SECRET',							'simetri2012');
define('SITE_HEADER',							'Simetri');
define('SITE_FOOTER',							'@ ' . date('Y') . ' PT Sinar Media 3');
define('LOGO_GEREJA',							'../temple.png');
define('IURAN_BULANAN_ID',						1);
define('IURAN_ANAK',							10000);
define('IURAN_DEWASA',							20000);

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

define('CONFIG',								'config');
define('GEREJA',								'gereja');
define('GROUP',								    'groups');
define('GROUP_PERMISSION',						'group_perms');
define('JEMAAT',								'jemaat');
define('JEMAAT_REKAP',							'jemaat_rekap');
define('JENIS_BIAYA',							'jenis_biaya');
define('KECAMATAN',								'kecamatan');
define('KELUARGA',								'keluarga');
define('KELURAHAN',								'kelurahan');
define('KOTA',								    'kota');
define('LOG',								    'log');
define('METODE_KIRIM',							'metode_kirim');
define('NEGARA',							    'negara');
define('PENDANAAN',				          		'pendanaan');
define('PERMISSION',				          	'permission');
define('PENDIDIKAN',				            'pendidikan');
define('PROFESI',				                'profesi');
define('PROPINSI',				                'propinsi');
define('SEKTOR',				                'sektor');
define('TAGIHAN',				                'tagihan');
define('TAGIHAN_TYPE',				            'tagihan_type');
define('USER',									'users');
define('USER_GROUP',							'user_groups');
define('USER_GEREJA',							'user_gereja');


/* End of file constants.php */
/* Location: ./application/config/constants.php */