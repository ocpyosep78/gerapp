<?php
	$SessionID = (isset($_GET['SessionID'])) ? $_GET['SessionID'] : '';
	
	$StoreParam = $this->session->userdata($SessionID);
	$StoreParam['limit'] = 10000;
	$ArrayJemaat = $this->M_Jemaat->GetArray($StoreParam);
	
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
	<td style="width: 200px;">Nama Gereja</td></tr>
<?php foreach ($ArrayJemaat as $Key => $Array) { ?>
	<tr>
		<td><?php echo $Array['nama']; ?></td>
		<td><?php echo $Array['nomor']; ?></td>
		<td><?php echo (empty($Array['tempatlahir'])) ? '&nbsp;' : $Array['tempatlahir']; ?></td>
		<td><?php echo (empty($Array['tgllahir'])) ? '&nbsp;' : $Array['tgllahir']; ?></td>
		<td><?php echo (empty($Array['GerejaNama'])) ? '&nbsp;' : $Array['GerejaNama']; ?></td></tr>
<?php } ?>
</table>