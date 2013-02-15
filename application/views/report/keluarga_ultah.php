<?php
	$Param = $_GET;
	$Param['date_start'] = (isset($Param['date_start'])) ? $Param['date_start'] : '';
	$Param['date_end'] = (isset($Param['date_end'])) ? $Param['date_end'] : '';
	
	$ArrayDateStart = ConvertDateToArray($Param['date_start']);
	$ArrayDateEnd = ConvertDateToArray($Param['date_end']);
	$StringCustom  = "AND (MONTH(ultah_perkawinan) >= '" . $ArrayDateStart['Month'] . "' AND DAY(ultah_perkawinan) >= '" . $ArrayDateStart['Day'] . "') ";
	$StringCustom .= "AND (MONTH(ultah_perkawinan) <= '" . $ArrayDateEnd['Month'] . "' AND DAY(ultah_perkawinan) <= '" . $ArrayDateEnd['Day'] . "') ";
	
	$ParamKeluarga['idgereja'] = $this->M_User->GetGerejaID();
	$ParamKeluarga['StringCustom'] = $StringCustom;
	$ParamKeluarga['limit'] = 100;
	$ParamKeluarga['sort'] = '[{"property":"ultah_perkawinan_monthday","direction":"ASC"}]';
	$ArrayKeluarga = $this->M_Keluarga->GetArray($ParamKeluarga);
?>

<?php if (count($ArrayKeluarga) > 0) { ?>
	<style>
		td { border: 1px solid #000000; }
		.center { text-align: center; }
	</style>
	
	<div style="padding: 0 0 15px 0;">
		<div class="center">Data Ulang Tahuan Keluarga</div>
		<div class="center"><?php echo GetFormatDateCommon($Param['date_start'], array('FormatDate' => "d F Y")) . ' - ' . GetFormatDateCommon($Param['date_end'], array('FormatDate' => "d F Y")); ?></div>
	</div>
	
	<table style="border-collapse: collapse; width: 100%;">
		<tr>
			<td class="center" style="width: 15%;">Nama Keluarga</td>
			<td class="center" style="width: 15%;">Nomor Anggota</td>
			<td class="center" style="width: 15%;">No KK</td>
			<td class="center" style="width: 10%;">No HP</td>
			<td class="center" style="width: 10%;">Ultah Perkawinan</td>
		</tr>
		<?php foreach ($ArrayKeluarga as $Key => $Record) { ?>
			<tr>
				<td><?php echo $Record['nama']; ?></td>
				<td><?php echo $Record['nomor']; ?></td>
				<td><?php echo $Record['no_kk']; ?></td>
				<td><?php echo $Record['no_hp']; ?></td>
				<td class="center"><?php echo GetFormatDateCommon($Record['ultah_perkawinan'], array('FormatDate' => "d F")); ?></td>
			</tr>
		<?php } ?>
	</table>
<?php } ?>