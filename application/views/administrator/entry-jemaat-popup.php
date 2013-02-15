<style>
.x-panel-body-default { background: #CED9E7; }

#Tab1-body, #Tab2-body, #Tab3-body { background: #FFFFFF; }

#TabJemaat .cnt { float: left; width: 310px; }
#TabJemaat .left { float: left; width: 110px; padding: 3px 5px 12px 0; text-align: right; }
#TabJemaat .right { float: left; width: 180px; }
#CntFoto img { width: 124px; height: 124px; }
</style>

<div style="padding: 5px; background: #FFFFFF;">
	<div style="width: 300px; margin: 0px auto;">
		<div style="float: left; width: 70px; padding: 3px 5px 12px 0; text-align: right;">Nomor :</div>
		<div style="float: left; width: 230px;"><div id="nomorED"></div></div>
		<div class="clear"></div>
		<div style="float: left; width: 70px; padding: 3px 5px 12px 0; text-align: right;">Nama :</div>
		<div style="float: left; width: 230px;"><div id="namaED"></div></div>
		<div class="clear"></div>
	</div>
</div>
<div id="TabJemaat"></div>
<div id="FormUpload" class="hidden"></div>

<div id="Cnt">
	<div id="UmumEL" class="x-hide-display">
		<div class="cnt">
			<div class="left">Keluarga :</div>
			<div class="right"><div id="keluargaED"></div></div>
			<div style="float: left; width: 20px;">
				<img id="ButtonFamily" src="<?php echo $this->config->item('base_url'); ?>/images/family.png" style="width: 20px; cursor: pointer;" alt="Keluarga" title="Keluarga" />
			</div>
			<div class="clear"></div>
			<div class="left">Hbg. Keluarga :</div>
			<div class="right"><div id="hubungankeluargaED"></div></div>
			<div class="clear"></div>
			<div class="left">Tempat Lahir :</div>
			<div class="right"><div id="tempatlahirED"></div></div>
			<div class="clear"></div>
			<div class="left">Tanggal Lahir :</div>
			<div class="right"><div id="tgllahirED"></div></div>
			<div class="clear"></div>
			<div class="left">Negara :</div>
			<div class="right"><div id="negaraED"></div></div>
			<div class="clear"></div>
			<div class="left">Propinsi :</div>
			<div class="right"><div id="propinsiED"></div></div>
			<div class="clear"></div>
			<div class="left">Kota :</div>
			<div class="right"><div id="kotaED"></div></div>
			<div class="clear"></div>
			<div class="left">Kecamatan :</div>
			<div class="right"><div id="kecamatanED"></div></div>
			<div class="clear"></div>
			<div class="left">Kelurahan :</div>
			<div class="right"><div id="kelurahanED"></div></div>
			<div class="clear"></div>
			<div class="left">Alamat :</div>
			<div class="right"><div id="alamatED"></div></div>
			<div class="clear"></div>
		</div>
		<div class="cnt">
			<div class="left">Rt / Rw :</div>
			<div class="right"><div id="rtrwED"></div></div>
			<div class="clear"></div>
			<div class="left">Tanggal Daftar :</div>
			<div class="right"><div id="tanggaldaftarED"></div></div>
			<div class="clear"></div>
			<div class="left">Gereja :</div>
			<div class="right"><div id="gerejaED"></div></div>
			<div class="clear"></div>
			<div class="left">Kodepos :</div>
			<div class="right"><div id="kodeposED"></div></div>
			<div class="clear"></div>
			<div class="left">Telepon :</div>
			<div class="right"><div id="telponED"></div></div>
			<div class="clear"></div>
			<div class="left">HP :</div>
			<div class="right"><div id="hpED"></div></div>
			<div class="clear"></div>
			<div class="left">Status :</div>
			<div class="right"><div id="statusED"></div></div>
			<div class="clear"></div>
			<div class="left">Meninggal :</div>
			<div class="right"><div id="meninggalED"></div></div>
			<div class="clear"></div>
			<div class="left">Tgl Meninggal :</div>
			<div class="right"><div id="tgl_meninggalED"></div></div>
			<div class="clear"></div>
			<div class="left">Dimakamkan di :</div>
			<div class="right"><div id="tempat_makamED"></div></div>
			<div class="clear"></div>
			<div class="left">Sektor :</div>
			<div class="right"><div id="sektorED"></div></div>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
	</div>

	<div id="DetailEL" class="x-hide-display">
		<div class="cnt">
			<div class="left">Firstname :</div>
			<div class="right"><div id="firstnameED"></div></div>
			<div class="clear"></div>
			<div class="left">Lastname :</div>
			<div class="right"><div id="lastnameED"></div></div>
			<div class="clear"></div>
			<div class="left">Email :</div>
			<div class="right"><div id="emailED"></div></div>
			<div class="clear"></div>
			<div class="left">Jenis Kelamin :</div>
			<div class="right"><div id="sexED"></div></div>
			<div class="clear"></div>
			<div class="left">Profesi :</div>
			<div class="right"><div id="profesiED"></div></div>
			<div class="clear"></div>
			<div class="left">Institusi :</div>
			<div class="right"><div id="institusiED"></div></div>
			<div class="clear"></div>
			<div class="left">Jabatan :</div>
			<div class="right"><div id="jabatanED"></div></div>
			<div class="clear"></div>
			<div class="left">Pendidikan :</div>
			<div class="right"><div id="pendidikanED"></div></div>
			<div class="clear"></div>
			<div class="left">Gelar :</div>
			<div class="right"><div id="gelarED"></div></div>
			<div class="clear"></div>
			<div class="left">Jurusan :</div>
			<div class="right"><div id="jurusanED"></div></div>
			<div class="clear"></div>
			<div class="left">Kelengkapan :</div>
			<div class="right"><div id="LengkapED"></div></div>
			<div class="clear"></div>
		</div>
		<div class="cnt" style="width: 330px;">
			<div class="left" style="width: 130px;">Status Baptis :</div>
			<div class="right"><div id="statusbaptisED"></div></div>
			<div class="clear"></div>
			<div class="left" style="width: 130px;">Tanggal Baptis :</div>
			<div class="right"><div id="tanggalbaptisED"></div></div>
			<div class="clear"></div>
			<div class="left" style="width: 130px;">Tempat Baptis :</div>
			<div class="right"><div id="tempat_baptisED"></div></div>
			<div class="clear"></div>
			<div class="left" style="width: 130px;">Status Sidi :</div>
			<div class="right"><div id="statussidiED"></div></div>
			<div class="clear"></div>
			<div class="left" style="width: 130px;">Tanggal Sidi :</div>
			<div class="right"><div id="tanggalsidiED"></div></div>
			<div class="clear"></div>
			<div class="left" style="width: 130px;">Tempat Sidi :</div>
			<div class="right"><div id="tempat_sidiED"></div></div>
			<div class="clear"></div>
			<div class="left" style="width: 130px;">Status Nikah :</div>
			<div class="right"><div id="statusnikahED"></div></div>
			<div class="clear"></div>
			<div class="left" style="width: 130px;">Tanggal Nikah :</div>
			<div class="right"><div id="tanggalnikahED"></div></div>
			<div class="clear"></div>
			<div class="left" style="width: 130px;">Tampat Pemberkatan :</div>
			<div class="right"><div id="tempatpemberkatanED"></div></div>
			<div class="clear"></div>
			<div class="left" style="width: 130px;">Golongan Darah :</div>
			<div class="right"><div id="golongandarahED"></div></div>
			<div class="clear"></div>
			<div class="left" style="width: 130px;">Catatan :</div>
			<div class="right"><div id="catatanED"></div></div>
			<div class="clear"></div>
		</div>
		<div class="clear"></div>
	</div>
	
	<div id="FotoEL" class="x-hide-display">
		<div class="cnt" style="width: 500px;">
			<div class="left">Foto :</div>
			<div class="right"><div id="fotoED"></div></div>
			<div class="clear"></div>
			<div class="left">&nbsp;</div>
			<div class="right"><div id="CntFoto"></div></div>
			<div class="clear"></div>
			<div class="left">&nbsp;</div>
			<div id="CntMessage"></div>
			<div class="clear"></div>
		</div>
	</div>
</div>