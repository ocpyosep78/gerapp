<?php
	$this->M_Jemaat_Rekap->CheckMonthly();
	$UserAdmin = $this->session->userdata('UserAdmin');
	$AccessGerejaID = $this->M_Permission->GetAccessGerejaID($UserAdmin);
	$this->M_Config->CheckDefault(array('gereja_id' => $AccessGerejaID));
	
	$ArrayProperty = array(
		'Kepala Keluarga' => $this->M_Jemaat->GetCount(array( 'filter' => '[{"type":"string","value":"01","field":"hubungankeluarga"}]' )),
		'Data Lengkap' => $this->M_Jemaat->GetCount(array( 'filter' => '[{"type":"string","value":"1","field":"lengkap"}]' )),
		'Belum Lengkap' => $this->M_Jemaat->GetCount(array( 'filter' => '[{"type":"string","value":"0","field":"lengkap"}]' )),
		'Registrasi bulan ini' => $this->M_Jemaat->GetCount(array( 'filter' => '[{"type":"custom","value":"0","field":"MONTH(tanggaldaftar) = \'04\'"}]' )),
		'Total' => $this->M_Jemaat->GetCount(),
	);
	
	$JumlahKepalaKeluarga = $this->M_Jemaat->GetCount(array( 'filter' => '[{"type":"string","value":"01","field":"hubungankeluarga"}]' ));
	$JumlahDataLengkap = $this->M_Jemaat->GetCount(array( 'filter' => '[{"type":"string","value":"1","field":"lengkap"}]' ));
	$JumlahBelumLengkap = $this->M_Jemaat->GetCount(array( 'filter' => '[{"type":"string","value":"0","field":"lengkap"}]' ));
	$JumlahBulanIni = $this->M_Jemaat->GetCount(array( 'filter' => '[{"type":"custom","value":"0","field":"MONTH(tanggaldaftar) = \'04\'"}]' ));
	$JumlahAnggota = $this->M_Jemaat->GetCount();
	
	$ArrayJemaat = $this->M_Jemaat->GetArray(array( 'filter' => '[{"type":"string","value":"%'.date("-m-").'","field":"tgllahir"}]', 'sort' => '[{"property":"tgllahir_monthday","direction":"ASC"}]' ));
	$ArrayKeluarga = $this->M_Keluarga->GetArray(array( 'filter' => '[{"type":"string","value":"%'.date("-m-").'","field":"ultah_perkawinan"}]', 'sort' => '[{"property":"ultah_perkawinan_monthday","direction":"ASC"}]' ));
	$ArrayKeluargaUpdate = $this->M_Keluarga->GetArray(array( 'sort' => '[{"property":"UpdateTime","direction":"DESC"}]', 'limit' => 7 ));
	
	$ArrayJemaatCount = $this->M_Jemaat_Rekap->GetChartLine();
	$ArraySex = $this->M_Jemaat->GetArrayJenisKelamin(array());
	$ArrayLengkap = $this->M_Jemaat->GetArrayLengkap(array());
	$ArrayProfesi = $this->M_Jemaat->GetArrayProfesi(array());
?>
<h1>Selamat datang di Sistem Informasi Jemaat</h1>

<style>
#Dashboard { overflow: auto; overflow-x: hidden; padding-right: 15px; }		/*	overide by js */
.box { float: left; width: 48%; }
.box .pad { padding: 10px 0 10px 0; }
</style>

<div class="x-hidden">
	<div id="Box1" class="x-hidden" style="padding: 10px;">
		<div id="GridProperty1"></div>
		<div id="GridProperty1Store" class="x-hidden"><?php echo json_encode($ArrayProperty); ?></div>
	</div>
	<div id="Box2" class="x-hidden" style="padding: 10px;">
		<div id="GridProperty2"></div>
		<div id="GridProperty2Store" class="x-hidden"><?php echo json_encode($ArrayKeluarga); ?></div>
	</div>
	<div id="Box3" class="x-hidden" style="padding: 10px;">
		<div id="GridProperty3"></div>
		<div id="GridProperty3Store" class="x-hidden"><?php echo json_encode($ArrayJemaat); ?></div>
	</div>
	<div id="Box4" class="x-hidden" style="padding: 10px;">
		<div id="GridProperty4"></div>
		<div id="GridProperty4Store" class="x-hidden"><?php echo json_encode($ArrayKeluargaUpdate); ?></div>
	</div>
	<div id="Box5" class="x-hidden" style="padding: 10px;">
		<div id="Box5Store"><?php echo json_encode($ArrayJemaatCount); ?></div>
	</div>
	<div id="Box6" class="x-hidden" style="padding: 10px;">
		<div id="Box6Store"><?php echo json_encode($ArraySex); ?></div>
	</div>
	<div id="Box7" class="x-hidden" style="padding: 10px;">
		<div id="Box7Store"><?php echo json_encode($ArrayLengkap); ?></div>
	</div>
	<div id="Box8" class="x-hidden" style="padding: 10px;">
		<div id="Box8Store"><?php echo json_encode($ArrayProfesi); ?></div>
	</div>
</div>

<div id="Dashboard">
	<div class="box">
		<div class="pad" id="CntBox1"></div>
		<div class="pad" id="CntBox3"></div>
		<div class="pad" id="CntBox5"></div>
		<div class="pad" id="CntBox7"></div>
	</div>
	<div class="box" style="float: right; margin: 0 5px 0 0;">
		<div class="pad" id="CntBox2"></div>
		<div class="pad" id="CntBox4"></div>
		<div class="pad" id="CntBox6"></div>
		<div class="pad" id="CntBox8"></div>
	</div>
	<div class="clear"></div>
	
	<div style="text-align: center;">
		<div style="width: 100px; margin: 0px auto;" id="BtnGraph"></div>
	</div>
</div>