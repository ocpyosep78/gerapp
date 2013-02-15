<?php
	$KeluargaID = (isset($_GET['KeluargaID'])) ? $_GET['KeluargaID'] : 0;
	$ArrayJemaat = $this->M_Jemaat->GetArray(array('idkeluarga' => $KeluargaID, 'sort' => '[{"property":"hubungankeluarga","direction":"ASC"}]'));
	$ArrayHubunganKeluarga = $this->M_Hubungan_Keluarga->GetHashArray();
	
	if (count($ArrayJemaat) <= 0) {
		echo 'Data Jemaat tidak ditemukan.';
		return;
	}
?>

<style>
table { border-collapse: collapse; }
td, tr { border: 1px solid #000000; padding: 2px 5px; }
</style>

<table>
<tr>
	<td style="width: 200px;">Nama</td>
	<td style="width: 100px;">Nomor</td>
	<td style="width: 150px;">Tempat Lahir</td>
	<td style="width: 100px;">Tanggal Lahir</td>
	<td style="width: 200px;">Hubungan Keluarga</td></tr>
<?php foreach ($ArrayJemaat as $Key => $Array) { ?>
	<tr>
		<td><?php echo $Array['nama']; ?></td>
		<td><?php echo $Array['nomor']; ?></td>
		<td><?php echo (empty($Array['tempatlahir'])) ? '&nbsp;' : $Array['tempatlahir']; ?></td>
		<td><?php echo (empty($Array['tgllahir'])) ? '&nbsp;' : $Array['tgllahir']; ?></td>
		<td><?php echo (empty($Array['hubungankeluarga'])) ? '&nbsp;' : $ArrayHubunganKeluarga[$Array['hubungankeluarga']]; ?></td></tr>
<?php } ?>
</table>